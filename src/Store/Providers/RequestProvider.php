<?php

namespace Streply\Store\Providers;

use Streply\Exceptions\StreplyException;
use Streply\Entity\EntityInterface;
use Streply\Request\Request;
use Streply\Streply;

class RequestProvider implements StoreProviderInterface
{
	/**
	 * @return string
	 */
	public function name(): string
	{
		return 'request';
	}

	/**
	 * @param EntityInterface $event
	 */
	public function push(EntityInterface $event): void
	{
		Request::execute($event->toJson());
	}

	/**
	 * @param string $traceId
	 * @return void
	 */
	public function close(string $traceId): void { }
}