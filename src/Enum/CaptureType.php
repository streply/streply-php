<?php

namespace Streply\Enum;

class CaptureType
{
	public const TYPE_ERROR = 'error';
	public const TYPE_ACTIVITY = 'activity';

	/**
	 * @return string[]
	 */
	public static function all(): array
	{
		return [CaptureType::TYPE_ERROR, CaptureType::TYPE_ACTIVITY];
	}
}