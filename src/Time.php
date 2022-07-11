<?php

namespace Streply;

/**
 *
 */
class Time
{
	/**
	 * @return float
	 */
	public static function loadTime(): float
	{
		return microtime(true);
	}
}
