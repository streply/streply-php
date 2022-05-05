<?php

namespace Streamly\Enum;

class CaptureType
{
	public const TYPE_ERROR = 'error';
	public const TYPE_ACTIVITY = 'activity';
	public const TYPE_MESSAGE = 'message';

	/**
	 * @return string[]
	 */
	public static function all(): array
	{
		return [CaptureType::TYPE_ERROR, CaptureType::TYPE_ACTIVITY, CaptureType::TYPE_MESSAGE];
	}
}