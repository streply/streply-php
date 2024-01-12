<?php

namespace Streply\Entity;

class User
{
    private string $userId;

    private string $userName;

    private array $params = [];

    public function __construct(string $userId, string $userName, array $params = [])
    {
        $this->userId = $userId;
        $this->userName = $userName;

        foreach ($params as $name => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }

            $this->params[] = [
                'name' => $name,
                'value' => $value,
            ];
        }
    }

    public function userId(): string
    {
        return $this->userId;
    }

    public function userName(): string
    {
        return $this->userName;
    }

    public function params(): array
    {
        return $this->params;
    }

    public function getValidationError(): ?string
    {
        // Params structure
        foreach ($this->params() as $param) {
            if (is_string($param['name']) === false) {
                return 'Param name wave wrong format (only: STRING)';
            }

            if (
                is_string($param['value']) === false &&
                is_int($param['value']) === false &&
                is_float($param['value']) === false &&
                is_null($param['value']) === false &&
                is_bool($param['value']) === false
            ) {
                return sprintf(
                    'User param %s have wrong value (only: NULL, STRING, INT, FLOAT, BOOL type)',
                    $param['name']
                );
            }
        }

        return null;
    }

    public function toArray(): array
    {
        return [
            'userId' => $this->userId,
            'userName' => $this->userName,
            'params' => $this->params,
        ];
    }
}
