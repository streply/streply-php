<?php

namespace Streamly\Store;

use Streamly\Store\Providers\StoreProviderInterface;
use Streamly\Entity\Event;
use Streamly\Entity\EntityInterface;
use Streamly\Exceptions\InvalidRequestException;
use Streamly\Streamly;

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
		// Validation
		$validationError = $event->getValidationError();

		if($validationError !== null) {
			throw new InvalidRequestException($validationError);
		}

		// Filter before send
		if(Streamly::getOptions()->has('filterBeforeSend') && $event instanceof Event) {
			$filterBeforeSend = Streamly::getOptions()->get('filterBeforeSend');

			if(is_callable($filterBeforeSend)) {
				$filterBeforeSendOutput = $filterBeforeSend($event);

				if($filterBeforeSendOutput === false) {
					return;
				}
			}
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
