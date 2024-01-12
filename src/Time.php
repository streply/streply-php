<?php

namespace Streply;

class Time
{
    public static function loadTime(): float
    {
        return microtime(true);
    }
}
