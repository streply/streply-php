<?php

namespace Streply\Input;

class Options
{
    private array $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    public function has(string $name): bool
    {
        return isset($this->options[$name]);
    }

    /**
     * @return mixed|null
     */
    public function get(string $name, $default = null)
    {
        return $this->options[$name] ?? $default;
    }

    public function set(string $name, $value): void
    {
        $this->options[$name] = $value;
    }
}
