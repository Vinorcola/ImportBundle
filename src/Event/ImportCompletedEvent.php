<?php

namespace Vinorcola\ImportBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;

class ImportCompletedEvent extends Event
{
    public const NAME = 'import.completed';

    /**
     * @var Response
     */
    private $response;

    /**
     * ImportCompletedEvent constructor.
     *
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
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
