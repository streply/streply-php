<?php

namespace Streamly\Store\Providers;

use Streamly\Exceptions\StreamlyException;
use Streamly\Entity\Event;
use Streamly\Request\Request;
use Streamly\Streamly;

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
	 * @param Event $event
	 */
	public function push(Event $event): void
	{
		Request::execute($event->toJson());
	}

	/**
	 * @param string $traceId
	 * @return void
	 */
	public function close(string $traceId): void { }
}