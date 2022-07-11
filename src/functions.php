<?php

declare(strict_types=1);

namespace Streply;

use Streply\Capture\Capture;
use Streply\Logs\Logs;
use Streply\Streply;
use Streply\Enum\Level;
use Streply\Request\Response;
use Streply\Entity\Breadcrumb;

/**
 * @return void
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
 * @return void
 * @throws Exceptions\NotInitializedException
 */
function Exception(\Throwable $exception, array $params = [], string $level = Level::NORMAL): void
{
	Capture::Error($exception, $params, $level);
}

/**
 * @param string $message
 * @param array $params
 * @param string|null $channel
 * @return void
 * @throws Exceptions\NotInitializedException
 */
function Activity(string $message, array $params = [], ?string $channel = null): void
{
	Capture::Activity($message, $params, $channel);
}

/**
 * @param string $type
 * @param string $message
 * @param array $params
 * @return void
 * @throws Exceptions\NotInitializedException
 */
function Breadcrumb(string $type, string $message, array $params = []): void
{
	Capture::Breadcrumb($type, $message, $params);
}

/**
 * @param string $message
 * @return void
 */
function Log(string $message)
{
	Logs::log($message);
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
function Close(): void
{
	Streply::Close();
}
