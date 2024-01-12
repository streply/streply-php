<?php

namespace Streply\Store\Providers;

use Streply\Entity\EntityInterface;
use Streply\Request\Request;

class RequestProvider implements StoreProviderInterface
{
    public function name(): string
    {
        return 'request';
    }

    public function push(EntityInterface $event): void
    {
        Request::execute($event->toJson());
    }

    public function close(string $traceId): void
    {
    }
}
