<?php

namespace Streply;

class Properties
{
    private array $parameters = [];

    public function collection(string $collectionName): array
    {
        return $this->parameters[$collectionName] ?? [];
    }

    public function has(string $collectionName, string $name): bool
    {
        return isset($this->parameters[$collectionName][$name]);
    }

    public function get(string $collectionName, string $name, $default = null)
    {
        if ($this->has($collectionName, $name) === false) {
            return $default;
        }

        return $this->parameters[$collectionName][$name];
    }

    public function delete(string $collectionName, string $name): void
    {
        if ($this->has($collectionName, $name) === true) {
            unset($this->parameters[$collectionName][$name]);
        }
    }

    private function set(string $collectionName, string $name, $value, bool $clearAfterRequest = false): void
    {
        $this->parameters[$collectionName][$name] = [
            'value' => $value,
            'clearAfterRequest' => $clearAfterRequest,
        ];
    }

    public function setForPerformance(string $name, $value, bool $clearAfterRequest = false): void
    {
        $this->set('performance', $name, $value, $clearAfterRequest);
    }

    public function setForEvent(string $name, $value, bool $clearAfterRequest = false): void
    {
        $this->set('event', $name, $value, $clearAfterRequest);
    }
}
