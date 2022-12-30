<?php

namespace Streply\Responses;

use Streply\Entity\Event;
use Streply\Enum\EventFlag;
use Streply\Exceptions\StreplyException;
use Streply\Streply;

class Entity
{
	private string $eventId;

	/**
	 * @param string $eventId
	 */
	public function __construct(string $eventId)
	{
		$this->eventId = $eventId;
	}

	/**
	 * @param string $flag
	 * @return Entity
	 */
	public function flag(string $flag): Entity
	{
		if(false === in_array($flag, EventFlag::all(), true)) {
			throw new StreplyException(
				sprintf(
					'%s is a invalid event flag',
					$flag
				)
			);
		}

		return $this->property('flag', $flag);
	}

	/**
	 * @param string $name
	 * @param $value
	 * @return Entity
	 */
	public function property(string $name, $value): Entity
	{
		Streply::Properties()->set($this->eventId, $name, $value);

		return $this;
	}
}
