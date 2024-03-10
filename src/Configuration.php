<?php

namespace Streply;

use Streply\Exceptions\ConfigurationException;

class Configuration
{
    public static function filterBeforeSend($callback): void
    {
        if (is_callable($callback) === false) {
            throw new ConfigurationException('The argument in filterBeforeSend method needs to be a function');
        }

        Streply::getOptions()->set('filterBeforeSend', $callback);
    }

    public static function setEnvironment(string $environment): void
    {
        Streply::getOptions()->set('environment', $environment);
    }

    public static function setRelease(string $release): void
    {
        Streply::getOptions()->set('release', $release);
    }

    public static function ignoreExceptions(array $exception): void
    {
        Streply::getOptions()->set('ignoreExceptions', $exception);
    }

    public static function backTraceInLogs(): void
    {
        Streply::getOptions()->set('backTraceInLogs', true);
    }
}
