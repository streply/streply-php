<?php

namespace Streply\Exceptions;

class UnhandledException extends \Exception
{
	/**
	 * @param string $message
	 * @param int $code
	 * @param string $file
	 */
	public function __construct(string $message, int $code, string $file)
	{
		parent::__construct($message, $code, null);

		$this->file = $file;
	}
}
