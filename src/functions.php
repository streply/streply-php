<?php

declare(strict_types=1);

namespace Streply;

use Streply\Capture\Capture;
use Streply\Enum\Level;
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

function Initialize(string $dsn, array $options = [])
{
    Streply::Initialize($dsn, $options);
}

function Exception(\Throwable $exception, array $params = [], string $level = Level::NORMAL): ?Entity
{
    $exception = Capture::Error($exception, $params, $level);

    if ($exception !== null) {
        $exception->sendImmediately();
    }

    return $exception;
}

function Activity(string $message, array $params = [], ?string $channel = null): ?Entity
{
    $activity = Capture::Activity($message, $params, $channel);

    if ($activity !== null) {
        $activity->sendImmediately();
    }

    return $activity;
}

function Log(string $message, array $params = [], string $level = Level::NORMAL): ?Entity
{
    $log = Capture::Log($message, $params, $level);

    if ($log !== null) {
        $log->sendImmediately();
    }

    return $log;
}

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
