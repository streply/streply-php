<?php

namespace Streamly\Store\Providers;

use Streamly\Entity\EntityInterface;

interface StoreProviderInterface
{
	/**
	 * @return string
	 */
	public function name(): string;

	/**
	 * @param EntityInterface $event
	 * @return void
	 */
	public function push(EntityInterface $event): void;

	/**
	 * @param string $traceId
	 * @return void
	 */
	public function close(string $traceId): void;
}
