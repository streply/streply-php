<?php

namespace Streamly;

use Streamly\Input\Dsn;
use Streamly\Input\Options;
use Streamly\Input\Http;
use Streamly\Input\Server;

class Streamly
{
	/**
	 * @var Streamly|null
	 */
	private static ?self $instance = null;

	/**
	 * @var Dsn|null
	 */
	private static ?Dsn $dsn = null;

	/**
	 * @var Options|null
	 */
	private static ?Options $options = null;

	/**
	 * @var Http|null
	 */
	private static ?Http $http = null;

	/**
	 * @var Server|null
	 */
	private static ?Server $server = null;

	/**
	 *
	 */
	protected function __construct() { }

	/**
	 * @return void
	 */
	protected function __clone() { }

	/**
	 * @param string $dsn
	 * @param array $options
	 * @return void
	 * @throws Exceptions\InvalidDsnException
	 */
	public static function Initialize(string $dsn, array $options = []): void
	{
		self::$dsn = new Dsn($dsn);
		self::$options = new Options($options);
		self::$http = new Http($_SERVER);
		self::$server = new Server();

		self::getInstance();

		\Streamly\Log(
			sprintf(
				'Initialize for %s',
				$dsn
			)
		);
	}

	/**
	 * @return bool
	 */
	public static function isInitialize(): bool
	{
		return self::$dsn !== null;
	}

	/**
	 * @return Dsn|null
	 */
	public static function getDsn(): ?Dsn
	{
		return self::$dsn;
	}

	/**
	 * @return Options|null
	 */
	public static function getOptions(): ?Options
	{
		return self::$options;
	}

	/**
	 * @return Http
	 */
	public static function getHttp(): Http
	{
		return self::$http;
	}

	/**
	 * @return Server
	 */
	public static function getServer(): Server
	{
		return self::$server;
	}

	/**
	 * @return Streamly
	 */
	public static function getInstance(): Streamly
	{
		if (self::$instance === null) {
			self::$instance = new static();
		}

		return self::$instance;
	}
}
