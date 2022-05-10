<?php

namespace Streamly\Store;

use Streamly\Store\Providers\StoreProviderInterface;
use Streamly\Entity\Event;
use Streamly\Entity\EntityInterface;
use Streamly\Exceptions\InvalidRequestException;

class Store
{
	/**
	 * @var StoreProviderInterface
	 */
	private StoreProviderInterface $storeProvider;

	/**
	 * @param StoreProviderInterface $storeProvider
	 */
	public function __construct(StoreProviderInterface $storeProvider)
	{
		$this->storeProvider = $storeProvider;
	}

	/**
	 * @param EntityInterface $event
	 * @return void
	 */
	public function push(EntityInterface $event): void
	{
		$validationError = $event->getValidationError();

		if($validationError !== null) {
			throw new InvalidRequestException($validationError);
		}

		$this->storeProvider->push($event);
	}

	/**
	 * @param string $traceId
	 * @return void
	 */
	public function close(string $traceId): void
	{
		$this->storeProvider->close($traceId);
	}
}
