<?php

namespace Streply;

class Scope
{
    private bool $isGlobalScope;

    public function __construct(bool $isGlobalScope = true)
    {
        $this->isGlobalScope = $isGlobalScope;
    }

    private function set(string $name, string $value): self
    {
        \Streply\Streply::Properties()->setForEvent(
            $name,
            $value,
            $this->isGlobalScope === false
        );

        return $this;
    }

    public function setChannel(string $channel): self
    {
        return $this->set('channel', $channel);
    }

    public function setFlag(string $flag): self
    {
        return $this->set('flag', $flag);
    }

    public function setRelease(string $release): self
    {
        return $this->set('release', $release);
    }

    public function setEnvironment(string $environment): self
    {
        return $this->set('release', $environment);
    }
}
