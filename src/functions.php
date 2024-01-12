<?php

declare(strict_types=1);

namespace Streply;

use Streply\Capture\Capture;
use Streply\Logs\Logs;
use Streply\Enum\Level;
use Streply\Responses\Entity;

/**
 * @return void
 * @throws Exceptions\StreplyException
 */
function ErrorHandler()
{
	$lastError = error_get_last();

	if(is_array($lastError)) {
		Exception(new Exceptions\UnhandledException(
			$lastError['message'],
			$lastError['line'],
			$lastError['file'],
		));
	}
}

register_shutdown_function('Streply\ErrorHandler');
set_error_handler("Streply\ErrorHandler");

/**
 * @param string $dsn
 * @param array $options
 * @return void
 * @throws Exceptions\InvalidDsnException
 */
function Initialize(string $dsn, array $options = [])
{
	Streply::Initialize($dsn, $options);
}

/**
 * @param \Throwable $exception
 * @param array $params
 * @param string $level
 * @return Entity|null
 * @throws Exceptions\StreplyException
 */
function Exception(\Throwable $exception, array $params = [], string $level = Level::NORMAL): ?Entity
{
	$exception = Capture::Error($exception, $params, $level);

    if($exception !== null) {
        $exception->sendImmediately();
    }

    return $exception;
}

/**
 * @param string $message
 * @param array $params
 * @param string|null $channel
 * @return Entity|null
 * @throws Exceptions\StreplyException
 */
function Activity(string $message, array $params = [], ?string $channel = null): ?Entity
{
	$activity = Capture::Activity($message, $params, $channel);

    if($activity !== null) {
        $activity->sendImmediately();
    }

    return $activity;
}

/**
 * @param string $message
 * @param array $params
 * @param string|null $channel
 * @param string $level
 * @return Entity|null
 */
function Log(string $message, array $params = [], ?string $channel = null, string $level = Level::NORMAL): ?Entity
{
	$log = Capture::Log($message, $params, $channel, $level);

    if($log !== null) {
        $log->sendImmediately();
    }

    return $log;
}

/**
 * @param string $userId
 * @param string|null $userName
 * @param array $params
 * @return void
 * @throws Exceptions\StreplyException
 */
function User(string $userId, ?string $userName = null, array $params = []): void
{
	Streply::User($userId, $userName, $params);
}

/**
 * @return array
 */
function Logs(): array
{
	return Logs::all();
}

/**
 * @return void
 */
function Flush(): void
{
	Streply::Flush();
}
