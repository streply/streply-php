<?php

namespace Streply\Logs;

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
	 * @return void
	 */
	public static function log(): void
	{
		foreach(func_get_args() as $message) {
			self::$logs[] = $message;
		}
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
