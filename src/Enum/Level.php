<?php

namespace Streply\Enum;

class Level
{
    public const CRITICAL = 'critical';

    public const HIGH = 'high';

    public const NORMAL = 'normal';

    public const LOW = 'low';

    /**
     * @return string[]
     */
    public static function all(): array
    {
        return [
            Level::CRITICAL,
            Level::HIGH,
            Level::NORMAL,
            Level::LOW,
        ];
    }
}
