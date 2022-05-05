<?php

namespace Streamly\Request;

use Streamly\Enum\CaptureType;
use Streamly\Enum\Level;
use Streamly\Entity\Record;

class Validator
{
	/**
	 * @var string|null
	 */
	private ?string $output = null;

	/**
	 * @param Record $record
	 * @return bool
	 */
	public function isValid(Record $record): bool
	{
		// Invalid record type
		if(in_array($record->getType(), CaptureType::all(), true) === false) {
			$this->output = sprintf('%s is a invalid type', $record->getType());

			return false;
		}

		// Level
		if(in_array($record->getLevel(), Level::all(), true) === false) {
			$this->output = sprintf('%s is a invalid level', $record->getLevel());

			return false;
		}

		// Params structure
		if(empty($record->getParams()) === false) {
			foreach($record->getParams() as $param) {
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
