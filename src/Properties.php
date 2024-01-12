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

    public function set(string $collectionName, string $name, $value): void
    {
        $this->parameters[$collectionName][$name] = $value;
    }

    /**
     * @return mixed|null
     */
    public function get(string $collectionName, string $name, $default = null)
    {
        if ($this->has($collectionName, $name) === false) {
            return $default;
        }

        return $this->parameters[$collectionName][$name];
    }

    public function setForPerformance(string $name, $value): void
    {
        $this->set('performance', $name, $value);
    }

    public function setForEvent(string $name, $value): void
    {
        $this->set('event', $name, $value);
    }
}
