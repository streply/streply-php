<?php

namespace Streply\Store\Providers;

use Streply\Entity\EntityInterface;
use Streply\Request\Request;
use Streply\Streply;

class MemoryProvider implements StoreProviderInterface
{
    private array $events = [];

    public function name(): string
    {
        return 'memory';
    }

    public function push(EntityInterface $event): void
    {
        $this->events[] = $event;
    }

    public function close(string $traceId): void
    {
        foreach ($this->events as $event) {
            // Filter before send
            if (Streply::getOptions()->has('filterBeforeSend')) {
                $filterBeforeSend = Streply::getOptions()->get('filterBeforeSend');

                if (is_callable($filterBeforeSend)) {
                    $filterBeforeSendOutput = $filterBeforeSend($event);

                    if ($filterBeforeSendOutput === false) {
                        continue;
                    }
                }
            }

            // Add properties to event
            if (method_exists($event, 'importFromProperties')) {
                $event->importFromProperties(Streply::Properties());
            }

            Request::execute($event->toJson());
        }
    }
}
