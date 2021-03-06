<?php

namespace Streply;

use Streply\Input\Dsn;
use Streply\Input\Options;
use Streply\Input\Http;
use Streply\Input\Server;
use Streply\Session;
use Streply\Store\Store;
use Streply\Store\Providers\RequestProvider;
use Streply\Store\Providers\StoreProviderInterface;
use Streply\Entity\Event;

class Streply
{
	/**
	 *
	 */
	public const API_VERSION = '0.0.17';

	/**
	 *
	 */
	private const UNIQUE_TRACE_ID_FORMAT = '%s_%d';

	/**
	 * @var Streply|null
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
	 * @var string
	 */
	private static string $traceId;

	/**
	 * @var int
	 */
	private static int $traceUniqueId;

	/**
	 * @var string
	 */
	private static string $sessionId;

	/**
	 * @var string
	 */
	private static string $userId;

	/**
	 * @var float
	 */
	public static float $startTime;

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
		if(isset($options['storeProvider']) === false || !($options['storeProvider'] instanceof StoreProviderInterface)) {
			$options['storeProvider'] = new RequestProvider();
		}
		
		self::$dsn = new Dsn($dsn);
		self::$options = new Options($options);
		self::$http = new Http($_SERVER);
		self::$server = new Server();
		self::$traceId = Session::traceId();
		self::$sessionId = Session::sessionId();
		self::$userId = Session::userId();
		self::$traceUniqueId = 0;
		self::$startTime = Time::loadTime();

		self::getInstance();

		// Log
		\Streply\Log(
			sprintf(
				'Initialize for %s',
				$dsn
			)
		);
	}

	/**
	 * @return string
	 */
	public static function traceId(): string
	{
		return self::$traceId;
	}

	/**
	 * @return string
	 */
	public static function traceUniqueId(): string
	{
		return sprintf(self::UNIQUE_TRACE_ID_FORMAT, self::$traceId, self::$traceUniqueId);
	}

	/**
	 * @return void
	 */
	public static function increaseTraceUniqueId(): void
	{
		++self::$traceUniqueId;
	}

	/**
	 * @return string
	 */
	public static function sessionId(): string
	{
		return self::$sessionId;
	}

	/**
	 * @return string
	 */
	public static function userId(): string
	{
		return self::$userId;
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
	 * @return void
	 */
	public static function Close(): void
	{
		// Event
		if(self::getOptions()->get('internalRequests', true) === true) {
			Activity('streamly.request');
		}

		// Close
		$store = new Store(Streply::$options->get('storeProvider'));
		$store->close(Streply::traceId());

		// Log
		\Streply\Log('Close');
	}

	/**
	 * @return Streply
	 */
	public static function getInstance(): Streply
	{
		if (self::$instance === null) {
			self::$instance = new static();
		}

		return self::$instance;
	}
}
