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
 * @return Response
 */
function Exception(\Exception $exception, array $params = [], string $level = Level::NORMAL): Response
{
	return Capture::Error($exception, $params, $level);
}

/**
 * @param string $message
 * @param array $params
 * @param string|null $channel
 * @param string $level
 * @return Response
 */
function Message(string $message, array $params = [], ?string $channel = null, string $level = Level::NORMAL): Response
{
	return Capture::Message($message, $params, $channel, $level);
}

/**
 * @param string $recordId
 * @param string $channel
 * @param array $params
 * @return Response
 */
function Activity(string $recordId, string $channel, array $params = []): Response
{
	return Capture::Activity($recordId, $channel, $params);
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
