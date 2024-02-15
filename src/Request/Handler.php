<?php

namespace Streply\Request;

use Streply\Entity\EntityInterface;
use Streply\Entity\Event;
use Streply\Streply;

class Handler
{
    private EntityInterface $event;

    public function __construct(EntityInterface $event)
    {
        $this->event = $event;
    }

    public function handle(): ?Response
    {
        if ($this->event->isAllowedRequest()) {
            $validationError = $this->event->getValidationError();

            if ($validationError !== null) {
                return Response::Error($validationError);
            }

            if (Streply::getOptions()->has('filterBeforeSend') && $this->event instanceof Event) {
                $filterBeforeSend = Streply::getOptions()->get('filterBeforeSend');

                if (is_callable($filterBeforeSend)) {
                    $filterBeforeSendOutput = $filterBeforeSend($this->event);

                    if ($filterBeforeSendOutput === false) {
                        return Response::Error('Request was filtered');
                    }
                }
            }

            if (method_exists($this->event, 'importFromProperties')) {
                $this->event->importFromProperties(Streply::Properties());
            }

            return Request::execute($this->event->toJson());
        }

        return Response::Error('Invalid request');
    }
}
