<?php

namespace Streply\Request;

class Response
{
    public const STATUS_SUCCESS = 'success';

    public const STATUS_ERROR = 'error';

    private array $response = [];

    public function __construct(string $response)
    {
        $decode = @json_decode($response, true);
        $this->response = is_array($decode) ? $decode : [];
    }

    public static function Error(string $errorMessage): Response
    {
        return new Response(json_encode([
            'status' => self::STATUS_ERROR,
            'data' => [
                'message' => $errorMessage,
            ],
        ]));
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
