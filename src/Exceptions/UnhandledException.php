<?php

namespace Streply\Exceptions;

class UnhandledException extends \Exception
{
    public function __construct(string $message, int $code, string $file)
    {
        parent::__construct($message, $code, null);

        $this->file = $file;
    }
}
