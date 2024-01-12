<?php

namespace Streply;

use Streply\Entity\User;
use Streply\Exceptions\InvalidUserException;
use Streply\Input\Dsn;
use Streply\Input\Http;
use Streply\Input\Options;
use Streply\Input\Server;
use Streply\Performance\Transactions;
use Streply\Store\Providers\MemoryProvider;
use Streply\Store\Store;

final class Streply
{
    public const API_VERSION = '0.0.47';

    private const UNIQUE_TRACE_ID_FORMAT = '%s_%d';

    private static ?self $instance = null;

    private static ?Dsn $dsn = null;

    private static ?Options $options = null;

    private static ?Http $http = null;

    private static ?Server $server = null;

    private static string $traceId;

    private static int $traceUniqueId;

    private static string $sessionId;

    private static string $userId;

    public static float $startTime;

    public static Transactions $performanceTransactions;

    public static ?User $user = null;

    public static Properties $properties;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @throws Exceptions\InvalidDsnException
     */
    public static function Initialize(string $dsn, array $options = []): void
    {
        if (isset($options['storeProvider']) === false) {
            $options['storeProvider'] = new MemoryProvider();
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
        self::$performanceTransactions = new Transactions();
        self::$properties = new Properties();

        self::getInstance();

        // Log
        Logs\Logs::Log(
            sprintf(
                'Initialize for %s',
                $dsn
            )
        );
    }

    public static function traceId(): string
    {
        return self::$traceId;
    }

    public static function traceUniqueId(): string
    {
        return sprintf(self::UNIQUE_TRACE_ID_FORMAT, self::$traceId, self::$traceUniqueId);
    }

    public static function increaseTraceUniqueId(): void
    {
        ++self::$traceUniqueId;
    }

    public static function sessionId(): string
    {
        return self::$sessionId;
    }

    public static function userId(): string
    {
        return self::$userId;
    }

    public static function isInitialize(): bool
    {
        return self::$dsn !== null;
    }

    public static function getDsn(): ?Dsn
    {
        return self::$dsn;
    }

    public static function getOptions(): ?Options
    {
        return self::$options;
    }

    public static function getHttp(): Http
    {
        return self::$http;
    }

    public static function getServer(): Server
    {
        return self::$server;
    }

    public static function getPerformanceTransactions(): Transactions
    {
        return self::$performanceTransactions;
    }

    public static function Flush(): void
    {
        if (true === self::isInitialize()) {
            // Close
            $store = new Store(Streply::$options->get('storeProvider'));
            $store->close(Streply::traceId());

            // Log
            Logs\Logs::Log('Flush');
        }
    }

    public static function getInstance(): Streply
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public static function User(string $userId, ?string $userName = null, array $params = []): void
    {
        self::$user = new User(
            $userId,
            $userName ?? $userId,
            $params
        );

        if (self::$user->getValidationError() !== null) {
            throw new InvalidUserException(self::$user->getValidationError());
        }
    }

    public static function Properties(): Properties
    {
        return self::$properties;
    }
}
