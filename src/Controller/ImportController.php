<?php

namespace Vinorcola\ImportBundle\Controller;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Vinorcola\HelperBundle\Controller;
use Vinorcola\ImportBundle\Config\Config;
use Vinorcola\ImportBundle\Event\ImportCompletedEvent;
use Vinorcola\ImportBundle\Exception\FileNotFoundException;
use Vinorcola\ImportBundle\Form\ImportType;
use Vinorcola\ImportBundle\Form\MappingType;
use Vinorcola\ImportBundle\Model\ImportModel;

class ImportController extends Controller
{
    private const SESSION_FILE_PATH = 'import:filepath';

    /**
     * @Route("/select-file", methods={"GET", "POST"}, name="import")
     *
     * @param Session     $session
     * @param Request     $request
     * @param string      $importName
     * @param Config      $config
     * @param ImportModel $model
     * @return Response
     */
    public function import(
        Session $session,
        Request $request,
        string $importName,
        Config $config,
        ImportModel $model
    ): Response {

        $form = $this->createForm(ImportType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($session->has(self::SESSION_FILE_PATH) && file_exists($session->get(self::SESSION_FILE_PATH))) {
                // Remove previous file.
                unlink($session->get(self::SESSION_FILE_PATH));
            }

            /** @var UploadedFile $file */
            $file = $form['file']->getData();
            $path = $model->moveFileToApplicationTemporaryDirectory($file);
            $session->set(self::SESSION_FILE_PATH, $path);

            return $this->redirectToRoute($config->getRouteNamePrefix($importName) . 'mapping');
        }

        return $this->render('@VinorcolaImport/Import/import.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/mapping", methods={"GET", "POST"}, name="mapping")
     *
     * @param Session                  $session
     * @param Request                  $request
     * @param string                   $importName
     * @param Config                   $config
     * @param ImportModel              $model
     * @param EventDispatcherInterface $eventDispatcher
     * @return Response
     */
    public function mapping(
        Session $session,
        Request $request,
        string $importName,
        Config $config,
        ImportModel $model,
        EventDispatcherInterface $eventDispatcher
    ): Response {

        set_time_limit(-1);

        if (!$session->has(self::SESSION_FILE_PATH)) {
            return $this->redirectToRoute($config->getRouteNamePrefix($importName) . 'import');
        }

        $filePath = $session->get(self::SESSION_FILE_PATH);
        $sheetIndex = $request->query->get('sheet', 0);
        try {
            $sheetNames = $model->getSheetNames($filePath);
            $sampleContent = $model->getSample($filePath, $sheetIndex);
        } catch (FileNotFoundException $exception) {
            $session->remove(self::SESSION_FILE_PATH);

            return $this->redirectToRoute($config->getRouteNamePrefix($importName) . 'import');
        }

        $form = $this->createForm(MappingType::class, null, [
            'labelPrefix' => $config->getRouteNamePrefix($importName) . 'mapping.column.',
            'mapping'     => $config->getMapping($importName),
            'headers'     => array_keys(current($sampleContent)),
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $model->process($importName, $filePath, $form->getData(), $sheetIndex);

            unlink($filePath);
            $session->remove(self::SESSION_FILE_PATH);

            $event = new ImportCompletedEvent(
                $importName,
                $this->redirectToRoute($config->getRouteNamePrefix($importName) . 'confirm')
            );
            $eventDispatcher->dispatch(ImportCompletedEvent::NAME, $event);

            return $event->getResponse();
        }

        return $this->render('@VinorcolaImport/Import/mapping.html.twig', [
            'sheetNames'    => $sheetNames,
            'currentSheet'  => $sheetIndex,
            'sampleContent' => $sampleContent,
            'form'          => $form->createView(),
        ]);
    }

    /**
     * @Route("/confirm", methods={"GET"}, name="confirm")
     *
     * @return Response
     */
    public function confirm(): Response
    {
        return $this->render('@VinorcolaImport/Import/confirm.html.twig');
    }
}
