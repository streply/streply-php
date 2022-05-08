<?php

namespace Streamly\Request;

use Streamly\Entity\Event;
use Streamly\Exceptions\StreamlyException;
use Streamly\Streamly;
use Streamly\Store\Store;
use Streamly\Store\Providers\StoreProviderInterface;

class Handler
{
	/**
	 * @param Event $event
	 * @return bool
	 */
	private static function isAllowedRequest(Event $event): bool
	{
		if(strpos($event->getRequestUri(), '/favicon.') !== false) {
			return false;
		}

		return true;
	}

	/**
	 * @param Event $event
	 * @return void
	 */
	public static function Handle(Event $event): void
	{
		if(self::isAllowedRequest($event)) {
			$storeProvider = Streamly::getOptions()->get('storeProvider');

			if(!($storeProvider instanceof StoreProviderInterface)) {
				throw new StreamlyException('Invalid store provider');
			}

			$store = new Store($storeProvider);
			$store->push($event);
		}
	}
}
