<?php

namespace Streply\Store;

use Streply\Store\Providers\StoreProviderInterface;
use Streply\Entity\Event;
use Streply\Entity\EntityInterface;
use Streply\Exceptions\InvalidRequestException;
use Streply\Streply;

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
		if(Streply::getOptions()->has('filterBeforeSend') && $event instanceof Event) {
			$filterBeforeSend = Streply::getOptions()->get('filterBeforeSend');

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
