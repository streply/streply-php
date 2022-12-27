<?php

namespace Streply;

use Streply\Streply;
use Streply\Exceptions\ConfigurationException;

class Configuration
{
	/**
	 * @param $callback
	 * @return void
	 */
	public static function filterBeforeSend($callback): void
	{
		if(is_callable($callback) === false) {
			throw new ConfigurationException('The argument in filterBeforeSend method needs to be a function');
		}

		Streply::getOptions()->set('filterBeforeSend', $callback);
	}

	/**
	 * @param string $environment
	 * @return void
	 */
	public static function setEnvironment(string $environment): void
	{
		Streply::getOptions()->set('environment', $environment);
	}

	/**
	 * @param string $release
	 * @return void
	 */
	public static function setRelease(string $release): void
	{
		Streply::getOptions()->set('release', $release);
	}
}
