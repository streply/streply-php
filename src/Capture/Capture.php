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
    private const SOURCE_LINE_NUMBERS = 10;

    public static function Exception(\Throwable $exception, array $params = [], string $level = Level::NORMAL): ?Entity
    {
        if (true === Streply::isInitialize()) {
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

            foreach ($exception->getTrace() as $trace) {
                if (isset($trace['file'], $trace['line'])) {
                    $event->addTrace(
                        $trace['file'],
                        $trace['line'],
                        $trace['function'] ?? null,
                        $trace['class'] ?? null,
                        $trace['args'] ?? [],
                        CodeSource::load($trace['file'], $trace['line'], self::SOURCE_LINE_NUMBERS)
                    );
                }
            }

            // Push
            Handler::Handle($event);

            return new Entity($event->getTraceUniqueId());
        }

        Logs::Log('Streply is not initialized');

        return null;
    }

    public static function Error(string $message, array $params = [], string $level = Level::NORMAL, ?string $channel = null): ?Entity
    {
        if (true === Streply::isInitialize()) {
            // Create record
            $event = Event::create(CaptureType::TYPE_ERROR, $message, $params, $level);
            $event->setChannel($channel);

            // Push
            Handler::Handle($event);

            return new Entity($event->getTraceUniqueId());
        }

        Logs::Log('Streply is not initialized');

        return null;
    }

    public static function Activity(string $message, array $params = [], ?string $channel = null): ?Entity
    {
        if (true === Streply::isInitialize()) {
            // Create record
            $event = Event::create(CaptureType::TYPE_ACTIVITY, $message, $params);
            $event->setChannel($channel);

            // Push
            Handler::Handle($event);

            return new Entity($event->getTraceUniqueId());
        }

        Logs::Log('Streply is not initialized');

        return null;
    }

    public static function Log(string $message, array $params = [], string $level = Level::NORMAL, ?string $channel = null): ?Entity
    {
        if (true === Streply::isInitialize()) {
            // Create record
            $event = Event::create(CaptureType::TYPE_LOG, $message, $params, $level);
            $event->setChannel($channel);

            // Push
            Handler::Handle($event);

            return new Entity($event->getTraceUniqueId());
        }

        Logs::Log('Streply is not initialized');

        return null;
    }
}
