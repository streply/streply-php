<?php

namespace Streply\Store\Providers;

use Streply\Exceptions\StreplyException;
use Streply\Entity\EntityInterface;
use Streply\Store\Providers\RequestProvider;
use Streply\Request\Request;

class MemoryProvider implements StoreProviderInterface
{
	/**
	 * @var array
	 */
	private array $events = [];

	/**
	 * @return string
	 */
	public function name(): string
	{
		return 'memory';
	}

	/**
	 * @param EntityInterface $event
	 * @return void
	 */
	public function push(EntityInterface $event): void
	{
		$this->events[] = $event;
	}

	/**
	 * @param string $traceId
	 * @return void
	 */
	public function close(string $traceId): void
	{
		foreach($this->events as $event) {
			Request::execute($event->toJson());
		}
	}
}
