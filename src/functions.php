<?php

declare(strict_types=1);

namespace Streply;

use Streply\Capture\Capture;
use Streply\Enum\Level;
use Streply\Exceptions\InvalidDsnException;
use Streply\Exceptions\InvalidUserException;
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
set_exception_handler(static function (\Throwable $exception) {
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
    return Capture::Exception($exception, $params, $level);
}

function Error(string $message, array $params = [], string $level = Level::NORMAL, ?string $channel = null): ?Entity
{
    return Capture::Error($message, $params, $level, $channel);
}

function Activity(string $message, array $params = [], ?string $channel = null, ?string $flag = null): ?Entity
{
    return Capture::Activity($message, $params, $channel, $flag);
}

function Log(string $message, array $params = [], string $level = Level::NORMAL, ?string $channel = null): ?Entity
{
    return Capture::Log($message, $params, $level, $channel);
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
