<?php

namespace Streply\Logs;

class Logs
{
    private static array $logs = [];

    public static function log(): void
    {
        foreach (func_get_args() as $message) {
            self::$logs[] = $message;
        }
    }

    public static function all(): array
    {
        return self::$logs;
    }
}
