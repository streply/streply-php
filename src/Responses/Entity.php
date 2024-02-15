<?php

namespace Streply\Responses;

use Streply\Entity\Event;
use Streply\Request\Response;

class Entity
{
    private Event $event;

    private Response $response;

    public function __construct(Event $event, Response $response)
    {
        $this->event = $event;
        $this->response = $response;
    }

    public function eventId(): string
    {
        return $this->event->getTraceUniqueId();
    }

    public function event(): Event
    {
        return $this->event;
    }

    public function response(): Response
    {
        return $this->response;
    }
}
