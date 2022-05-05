<?php

namespace Streamly\Logs;

/**
 *
 */
class Logs
{
	/**
	 * @var array
	 */
	private static array $logs = [];

	/**
	 * Add log to logs array
	 *
	 * @param string $message
	 * @return void
	 */
	public static function log(string $message): void
	{
		self::$logs[] = $message;
	}

	/**
	 * Return all logs
	 *
	 * @return array
	 */
	public static function all(): array
	{
		return self::$logs;
	}
}
