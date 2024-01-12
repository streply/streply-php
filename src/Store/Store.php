<?php

namespace Streply\Store;

use Streply\Entity\EntityInterface;
use Streply\Entity\Event;
use Streply\Exceptions\InvalidRequestException;
use Streply\Store\Providers\StoreProviderInterface;
use Streply\Streply;

class Store
{
    private StoreProviderInterface $storeProvider;

    public function __construct(StoreProviderInterface $storeProvider)
    {
        $this->storeProvider = $storeProvider;
    }

    public function push(EntityInterface $event): void
    {
        // Validation
        $validationError = $event->getValidationError();

        if ($validationError !== null) {
            throw new InvalidRequestException($validationError);
        }

        // Filter before send
        if (Streply::getOptions()->has('filterBeforeSend') && $event instanceof Event) {
            $filterBeforeSend = Streply::getOptions()->get('filterBeforeSend');

            if (is_callable($filterBeforeSend)) {
                $filterBeforeSendOutput = $filterBeforeSend($event);

                if ($filterBeforeSendOutput === false) {
                    return;
                }
            }
        }

        $this->storeProvider->push($event);
    }

    public function close(string $traceId): void
    {
        $this->storeProvider->close($traceId);
    }
}
