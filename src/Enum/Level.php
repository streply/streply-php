<?php

namespace Streamly\Enum;

class Level
{
	public const CRITICAL = 'critical';
	public const NORMAL = 'normal';

	/**
	 * @return string[]
	 */
	public static function all(): array
	{
		return [Level::NORMAL, Level::CRITICAL];
	}
}