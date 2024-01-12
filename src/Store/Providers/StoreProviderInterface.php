<?php

namespace Streply\Store\Providers;

use Streply\Entity\EntityInterface;

interface StoreProviderInterface
{
    public function name(): string;

    public function push(EntityInterface $event): void;

    public function close(string $traceId): void;
}
