<?php

namespace Streply\Enum;

class EventFlag
{
    public const WITHOUT_FLAG = null;

    public const COMMAND = 'command';

    public static function all(): array
    {
        return [self::WITHOUT_FLAG, self::COMMAND];
    }
}
