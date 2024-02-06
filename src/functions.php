<?php

declare(strict_types=1);

namespace Streply;

use Streply\Capture\Capture;
use Streply\Enum\Level;
use Streply\Exceptions\InvalidDsnException;
use Streply\Exceptions\InvalidUserException;
use Streply\Exceptions\StreplyException;
use Streply\Logs\Logs;
use Streply\Responses\Entity;

function ErrorHandler()
{
    $lastError = error_get_last();

    if (is_array($lastError)) {
        Exception(new Exceptions\UnhandledException(
            $lastError['message'],
            $lastError['line'],
            $lastError['file'],
        ));
    }
}

register_shutdown_function('Streply\ErrorHandler');
set_error_handler("Streply\ErrorHandler");
set_exception_handler(static function(\Throwable $exception)  {
    Exception($exception);
});

/**
 * @throws InvalidDsnException
 */
function Initialize(string $dsn, array $options = [])
{
    Streply::Initialize($dsn, $options);
}

function Exception(\Throwable $exception, array $params = [], string $level = Level::NORMAL): ?Entity
{
    $exception = Capture::Exception($exception, $params, $level);

    if ($exception !== null) {
        $exception->sendImmediately();
    }

    return $exception;
}

function Error(string $message, array $params = [], string $level = Level::NORMAL, ?string $channel = null): ?Entity
{
    $error = Capture::Error($message, $params, $level, $channel);

    if ($error !== null) {
        $error->sendImmediately();
    }

    return $error;
}

/**
 * @throws StreplyException
 */
function Activity(string $message, array $params = [], ?string $channel = null, ?string $command = null): ?Entity
{
    $activity = Capture::Activity($message, $params, $channel);

    if($command !== null) {
        $activity->flag($command);
    }

    if ($activity !== null) {
        $activity->sendImmediately();
    }

    return $activity;
}

function Log(string $message, array $params = [], string $level = Level::NORMAL, ?string $channel = null): ?Entity
{
    $log = Capture::Log($message, $params, $level, $channel);

    if ($log !== null) {
        $log->sendImmediately();
    }

    return $log;
}

/**
 * @throws InvalidUserException
 */
function User(string $userId, ?string $userName = null, array $params = []): void
{
    Streply::User($userId, $userName, $params);
}

function Logs(): array
{
    return Logs::all();
}

function Flush(): void
{
    Streply::Flush();
}
