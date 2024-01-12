<?php

namespace Streply\Request;

class Response
{
    private const STATUS_SUCCESS = 'success';

    private const STATUS_ERROR = 'error';

    private array $response;

    public function __construct(string $response)
    {
        $decode = @json_decode($response, true);
        $this->response = is_array($decode) ? $decode : [];
    }

    public function isSuccess(): bool
    {
        return isset($this->response['status']) && $this->response['status'] === self::STATUS_SUCCESS;
    }

    public function getError(): ?string
    {
        return $this->response['data']['message'] ?? null;
    }

    public function getOutput(): array
    {
        return $this->response['data'] ?? [];
    }
}
