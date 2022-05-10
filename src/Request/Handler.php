<?php

namespace Streamly\Request;

use Streamly\Entity\EntityInterface;
use Streamly\Exceptions\StreamlyException;
use Streamly\Streamly;
use Streamly\Store\Store;
use Streamly\Store\Providers\StoreProviderInterface;

class Handler
{
	/**
	 * @param EntityInterface $event
	 * @return void
	 */
	public static function Handle(EntityInterface $event): void
	{
		if($event->isAllowedRequest()) {
			$storeProvider = Streamly::getOptions()->get('storeProvider');

			if(!($storeProvider instanceof StoreProviderInterface)) {
				throw new StreamlyException('Invalid store provider');
			}

			// Store request
			$store = new Store($storeProvider);
			$store->push($event);
		}
	}
}
