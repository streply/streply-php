<?php

namespace Streply\Entity;

interface EntityInterface
{
    public function getTraceId(): string;

    public function toJson(): string;

    public function getValidationError(): ?string;

    public function isAllowedRequest(): bool;
}
