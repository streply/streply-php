<?php

namespace Streply\Request;

use Streply\Entity\EntityInterface;
use Streply\Exceptions\StreplyException;
use Streply\Streply;
use Streply\Store\Store;
use Streply\Store\Providers\StoreProviderInterface;

class Handler
{
	/**
	 * @param EntityInterface $event
	 * @return void
	 */
	public static function Handle(EntityInterface $event): void
	{
		if($event->isAllowedRequest()) {
			$storeProvider = Streply::getOptions()->get('storeProvider');

			if(!($storeProvider instanceof StoreProviderInterface)) {
				throw new StreplyException('Invalid store provider');
			}

			// Store request
			$store = new Store($storeProvider);
			$store->push($event);
		}
	}
}
