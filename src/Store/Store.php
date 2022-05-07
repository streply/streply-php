<?php

namespace Streamly\Store;

use Streamly\Store\Providers\StoreProviderInterface;
use Streamly\Entity\Event;
use Streamly\Exceptions\InvalidRequestException;
use Streamly\Request\Validator;

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
	 * @param Event $event
	 * @return void
	 */
	public function push(Event $event): void
	{
		$validator = new Validator();

		if($validator->isValid($event) === false) {
			throw new InvalidRequestException($validator->output());
		}

		// Create log
		\Streamly\Log(
			sprintf(
				'Capture type:%s, message:%s, level:%s, provider:%s',
				$event->getType(),
				$event->getMessage(),
				$event->getLevel(),
				$this->storeProvider->name()
			)
		);

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
