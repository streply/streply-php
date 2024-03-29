<?php

namespace Streply\Capture;

use Streply\CodeSource;
use Streply\Entity\Event;
use Streply\Enum\CaptureType;
use Streply\Enum\Level;
use Streply\Logs\Logs;
use Streply\Request\Handler;
use Streply\Responses\Entity;
use Streply\Streply;

class Capture
{
    public const SOURCE_LINE_NUMBERS = 10;

    public static function Exception(\Throwable $exception, array $params = [], string $level = Level::NORMAL): ?Entity
    {
        if (true === Streply::isInitialize()) {
            // Ignore exceptions
            if (Streply::getOptions()->has('ignoreExceptions')) {
                $ignoredExceptions = Streply::getOptions()->get('ignoreExceptions');

                if (is_array($ignoredExceptions)) {
                    foreach ($ignoredExceptions as $ignoredException) {
                        if ($exception instanceof $ignoredException) {
                            return null;
                        }
                    }
                }
            }

            // Create record
            $event = Event::create(CaptureType::TYPE_ERROR, $exception->getMessage(), $params, $level);
            $event->setFile($exception->getFile());
            $event->setLine($exception->getLine());

            // Custom HTTP status code
            if (method_exists($exception, 'getStatusCode')) {
                $exceptionStatusCode = $exception->getStatusCode();

                if (is_int($exceptionStatusCode)) {
                    $event->setHttpStatusCode($exceptionStatusCode);
                }
            }

            $reflectionClass = new \ReflectionClass($exception);

            $event->setExceptionName($reflectionClass->getName());
            $event->setExceptionFileName($reflectionClass->getFileName());

            if ($reflectionClass->getParentClass() !== false) {
                $event->setParentExceptionName($reflectionClass->getParentClass()->getName());
                $event->setParentExceptionFileName($reflectionClass->getParentClass()->getFileName());
            }

            // Trace
            $event->addTrace(
                $exception->getFile(),
                $exception->getLine(),
                null,
                null,
                [],
                CodeSource::load($exception->getFile(), $exception->getLine(), self::SOURCE_LINE_NUMBERS)
            );
            $event->addTraces($exception->getTrace());

            // Push
            $handler = new Handler($event);

            return new Entity($event, $handler->handle());
        }

        Logs::Log('Streply is not initialized');

        return null;
    }

    public static function Error(string $message, array $params = [], string $level = Level::NORMAL): ?Entity
    {
        if (true === Streply::isInitialize()) {
            // Create record
            $event = Event::create(CaptureType::TYPE_ERROR, $message, $params, $level);
            $event->addTraces(debug_backtrace());

            // Push
            $handler = new Handler($event);

            return new Entity($event, $handler->handle());
        }

        Logs::Log('Streply is not initialized');

        return null;
    }

    public static function Activity(string $message, array $params = []): ?Entity
    {
        if (true === Streply::isInitialize()) {
            // Create record
            $event = Event::create(CaptureType::TYPE_ACTIVITY, $message, $params);

            // Push
            $handler = new Handler($event);

            return new Entity($event, $handler->handle());
        }

        Logs::Log('Streply is not initialized');

        return null;
    }

    public static function Log(string $message, array $params = []): ?Entity
    {
        if (true === Streply::isInitialize()) {
            // Create record
            $event = Event::create(CaptureType::TYPE_LOG, $message, $params);

            if (Streply::getOptions()->get('backTraceInLogs', false) === true) {
                $event->addTraces(debug_backtrace());
            }

            // Push
            $handler = new Handler($event);

            return new Entity($event, $handler->handle());
        }

        Logs::Log('Streply is not initialized');

        return null;
    }
}
