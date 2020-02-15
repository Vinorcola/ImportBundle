<?php

namespace Vinorcola\ImportBundle\Event;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

class ImportCompletedEvent extends Event
{
    /**
     * @var string
     */
    private $importName;

    /**
     * @var Response
     */
    private $response;

    /**
     * ImportCompletedEvent constructor.
     *
     * @param string   $importName
     * @param Response $response
     */
    public function __construct(string $importName, Response $response)
    {
        $this->importName = $importName;
        $this->response = $response;
    }

    /**
     * @return string
     */
    public function getImportName(): string
    {
        return $this->importName;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }
}
