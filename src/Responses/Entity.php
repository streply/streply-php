<?php

namespace Streply\Responses;

use Streply\Enum\EventFlag;
use Streply\Exceptions\StreplyException;
use Streply\Streply;

class Entity
{
    private string $eventId;

    public function __construct(string $eventId)
    {
        $this->eventId = $eventId;
    }

    public function flag(string $flag): Entity
    {
        if (false === in_array($flag, EventFlag::all(), true)) {
            throw new StreplyException(
                sprintf(
                    '%s is a invalid event flag',
                    $flag
                )
            );
        }

        return $this->property('flag', $flag);
    }

    public function property(string $name, $value): Entity
    {
        Streply::Properties()->set($this->eventId, $name, $value);

        return $this;
    }

    public function sendImmediately(): Entity
    {
        Streply::Flush(false);

        return $this;
    }
}
