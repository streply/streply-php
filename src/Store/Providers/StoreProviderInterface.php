<?php

namespace Streamly\Store\Providers;

use Streamly\Entity\Event;

interface StoreProviderInterface
{
	/**
	 * @return string
	 */
	public function name(): string;

	/**
	 * @param Event $event
	 * @return void
	 */
	public function push(Event $event): void;

	/**
	 * @param string $traceId
	 * @return void
	 */
	public function close(string $traceId): void;
}
