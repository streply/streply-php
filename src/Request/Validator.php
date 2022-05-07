<?php

namespace Streamly\Request;

use Streamly\Enum\CaptureType;
use Streamly\Enum\Level;
use Streamly\Entity\Event;

class Validator
{
	/**
	 * @var string|null
	 */
	private ?string $output = null;

	/**
	 * @param Event $event
	 * @return bool
	 */
	public function isValid(Event $event): bool
	{
		// Invalid record type
		if(in_array($event->getType(), CaptureType::all(), true) === false) {
			$this->output = sprintf('%s is a invalid type', $event->getType());

			return false;
		}

		// Level
		if(in_array($event->getLevel(), Level::all(), true) === false) {
			$this->output = sprintf('%s is a invalid level', $event->getLevel());

			return false;
		}

		// Params structure
		if(empty($event->getParams()) === false) {
			foreach($event->getParams() as $param) {
				if(
					is_string($param['value']) === false &&
					is_int($param['value']) === false &&
					is_float($param['value']) === false
				) {
					$this->output = sprintf('Param %s have wrong value (only: STRING, INT, FLOAT type)', $param['name']);

					return false;
				}
			}
		}

		return true;
	}

	/**
	 * @return string|null
	 */
	public function output(): ?string
	{
		return $this->output;
	}
}
