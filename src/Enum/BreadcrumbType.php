<?php

namespace Streply\Enum;

class BreadcrumbType
{
	public const INFO = 'info';
	public const DEBUG = 'debug';
	public const ERROR = 'error';
	public const QUERY = 'query';

	/**
	 * @return string[]
	 */
	public static function all(): array
	{
		return [
			self::INFO,
			self::DEBUG,
			self::ERROR,
			self::QUERY,
		];
	}
}
