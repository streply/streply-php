<?php

declare(strict_types=1);

namespace Streamly;

use Streamly\Capture\Capture;
use Streamly\Logs\Logs;
use Streamly\Streamly;
use Streamly\Enum\Level;
use Streamly\Request\Response;

/**
 * @param string $dsn
 * @param array $options
 * @return void
 */
function Initialize(string $dsn, array $options = [])
{
	Streamly::Initialize($dsn, $options);
}

/**
 * @param \Exception $exception
 * @param array $params
 * @param string $level
 * @return void
 */
function Exception(\Exception $exception, array $params = [], string $level = Level::NORMAL): void
{
	Capture::Error($exception, $params, $level);
}

/**
 * @param string $message
 * @param array $params
 * @param string|null $channel
 * @param string $level
 * @return void
 */
function Message(string $message, array $params = [], ?string $channel = null, string $level = Level::NORMAL): void
{
	Capture::Message($message, $params, $channel, $level);
}

/**
 * @param string $message
 * @param string|null $channel
 * @param array $params
 * @return void
 */
function Activity(string $message, ?string $channel = null, array $params = []): void
{
	Capture::Activity($message, $channel, $params);
}

/**
 * Function helper
 *
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
	Streamly::Close();
}