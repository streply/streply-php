<?php

namespace Streply\Entity;

use Streply\Enum\CaptureType;
use Streply\Enum\Level;
use Streply\Input\Dsn;
use Streply\Input\Http;
use Streply\Input\Options;
use Streply\Input\Server;
use Streply\Properties;
use Streply\Streply;
use Streply\Time;

class Event implements EntityInterface
{
    private const ALLOWED_PARAMETERS = ['flag', 'release', 'environment', 'channel'];

    private string $traceId;

    private string $traceUniqueId;

    private string $sessionId;

    private string $userId;

    private int $status;

    private string $type;

    private int $projectId;

    private string $message;

    private string $level;

    private \DateTime $date;

    private float $time;

    private array $params;

    private array $trace = [];

    private ?string $release = null;

    private ?string $environment = null;

    private ?string $channel = null;

    private ?string $file = null;

    private ?int $line = null;

    private ?int $httpStatusCode = null;

    private ?string $url = null;

    private string $technology;

    private string $technologyVersion;

    private string $requestUserAgent;

    private ?string $requestServer;

    private ?int $requestPort;

    private ?string $requestScheme;

    private ?string $requestMethod;

    private ?string $requestUri;

    private ?int $requestTime;

    private ?float $requestTimeFloat;

    private ?string $requestIp;

    private \DateTimeZone $dateTimeZone;

    private ?string $requestContentType;

    private ?array $requestParams;

    private array $requestHeaders;

    private ?float $serverCpuLoad;

    private ?float $serverDiskFreeSpace;

    private ?float $serverDiskTotalSpace;

    private int $serverMemoryUsage;

    private int $serverMemoryPeakUsage;

    private ?string $exceptionName = null;

    private ?string $exceptionFileName = null;

    private ?string $parentExceptionName = null;

    private ?string $parentExceptionFileName = null;

    private float $loadTime;

    private float $startTime;

    private string $apiClientVersion;

    private ?string $flag;

    private ?string $dir;

    public static function create(string $type, string $message, array $params = [], string $level = Level::NORMAL): Event
    {
        $record = new Event(
            Streply::getOptions(),
            Streply::getDsn(),
            Streply::getHttp(),
            Streply::getServer()
        );
        $record->setType($type);
        $record->setParams($params);
        $record->setLevel($level);
        $record->setMessage($message);

        return $record;
    }

    public function __construct(
        Options $options,
        Dsn $dsn,
        Http $http,
        Server $server
    ) {
        // Increase trace unique ID
        Streply::increaseTraceUniqueId();

        $now = new \DateTime();

        $this->traceId = Streply::traceId();
        $this->traceUniqueId = Streply::traceUniqueId();
        $this->sessionId = Streply::sessionId();
        $this->userId = Streply::userId();
        $this->status = 0;
        $this->level = \Streply\Enum\Level::NORMAL;
        $this->date = $now;
        $this->startTime = Streply::$startTime;
        $this->time = Time::loadTime();
        $this->loadTime = Time::loadTime() - Streply::$startTime;
        $this->technology = 'php';
        $this->technologyVersion = PHP_VERSION;
        $this->params = [];
        $this->dateTimeZone = $now->getTimezone();
        $this->environment = $options->get('environment', null);
        $this->release = $options->get('release', null);
        $this->projectId = $dsn->getProjectId();
        $this->requestUserAgent = $http->getUserAgent();
        $this->requestServer = $http->getServerSoftware();
        $this->requestPort = $http->getPort();
        $this->requestScheme = $http->getScheme();
        $this->requestMethod = $http->getMethod();
        $this->requestUri = $http->getUri();
        $this->requestTime = $http->getTime();
        $this->requestTimeFloat = $http->getTimeFloat();
        $this->requestContentType = $http->getContentType();
        $this->requestParams = $http->getRequestParams();
        $this->requestHeaders = $http->getHeaders();
        $this->requestIp = $http->getIp();
        $this->httpStatusCode = $http->getStatusCode();
        $this->url = $http->getUrl();
        $this->serverCpuLoad = $server->getCpuLoad();
        $this->serverDiskFreeSpace = $server->getDiskFreeSpace();
        $this->serverDiskTotalSpace = $server->getDiskTotalSpace();
        $this->serverMemoryUsage = $server->getMemoryUsage();
        $this->serverMemoryPeakUsage = $server->getMemoryPeakUsage();
        $this->apiClientVersion = Streply::API_VERSION;
        $this->flag = null;
        $this->dir = getcwd() === false ? null : getcwd();
    }

    public function getTraceUniqueId(): string
    {
        return $this->traceUniqueId;
    }

    public function setTraceUniqueId(string $traceUniqueId): void
    {
        $this->traceUniqueId = $traceUniqueId;
    }

    public function setParams(array $params): void
    {
        foreach ($params as $name => $value) {
            $this->addParam($name, $value);
        }
    }

    public function addParam(string $name, $value): void
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }

        $this->params[] = [
            'name' => $name,
            'value' => $value,
        ];
    }

    public function getTraceId(): string
    {
        return $this->traceId;
    }

    public function setTraceId(string $traceId): void
    {
        $this->traceId = $traceId;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function setLevel(string $level): void
    {
        $this->level = $level;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getChannel(): ?string
    {
        return $this->channel;
    }

    public function setChannel(?string $channel): void
    {
        $this->channel = $channel;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): void
    {
        $this->file = $file;
    }

    public function getLine(): ?int
    {
        return $this->line;
    }

    public function setLine(?int $line): void
    {
        $this->line = $line;
    }

    public function getExceptionName(): ?string
    {
        return $this->exceptionName;
    }

    public function setExceptionName(?string $exceptionName): void
    {
        $this->exceptionName = $exceptionName;
    }

    public function getExceptionFileName(): ?string
    {
        return $this->exceptionFileName;
    }

    public function setExceptionFileName(?string $exceptionFileName): void
    {
        $this->exceptionFileName = $exceptionFileName;
    }

    public function getParentExceptionName(): ?string
    {
        return $this->parentExceptionName;
    }

    public function setParentExceptionName(?string $parentExceptionName): void
    {
        $this->parentExceptionName = $parentExceptionName;
    }

    public function getParentExceptionFileName(): ?string
    {
        return $this->parentExceptionFileName;
    }

    public function setParentExceptionFileName(?string $parentExceptionFileName): void
    {
        $this->parentExceptionFileName = $parentExceptionFileName;
    }

    public function getRequestUri(): ?string
    {
        return $this->requestUri;
    }

    public function setHttpStatusCode(?int $httpStatusCode): void
    {
        $this->httpStatusCode = $httpStatusCode;
    }

    public function setLoadTime(float $loadTime): void
    {
        $this->loadTime = $loadTime;
    }

    public function addTrace(string $file, int $line, ?string $function = null, ?string $class = null, array $args = [], array $source = []): void
    {
        $this->trace[] = [
            'file' => $file,
            'line' => $line,
            'function' => $function,
            'class' => $class,
            'args' => [],
            'source' => $source,
        ];
    }

    public function getFlag(): ?string
    {
        return $this->flag;
    }

    public function setFlag(?string $flag): void
    {
        $this->flag = $flag;
    }

    public function toArray(): array
    {
        return [
            'eventType' => 'event',
            'traceId' => $this->traceId,
            'traceUniqueId' => $this->traceUniqueId,
            'sessionId' => $this->sessionId,
            'userId' => $this->userId,
            'status' => $this->status,
            'type' => $this->type,
            'projectId' => $this->projectId,
            'message' => $this->message,
            'level' => $this->level,
            'date' => $this->date->format('Y-m-d H:i:s'),
            'startTime' => $this->startTime,
            'time' => $this->time,
            'loadTime' => $this->loadTime,
            'params' => $this->params,
            'trace' => $this->trace,
            'release' => $this->release,
            'environment' => $this->environment,
            'channel' => $this->channel,
            'file' => $this->file,
            'line' => $this->line,
            'httpStatusCode' => $this->httpStatusCode,
            'url' => $this->url,
            'technology' => $this->technology,
            'technologyVersion' => $this->technologyVersion,
            'requestUserAgent' => $this->requestUserAgent,
            'requestServer' => $this->requestServer,
            'requestPort' => $this->requestPort,
            'requestScheme' => $this->requestScheme,
            'requestMethod' => $this->requestMethod,
            'requestUri' => $this->requestUri,
            'requestTime' => $this->requestTime,
            'requestTimeFloat' => $this->requestTimeFloat,
            'requestContentType' => $this->requestContentType,
            'requestParams' => $this->requestParams,
            'requestHeaders' => $this->requestHeaders,
            'requestIp' => $this->requestIp,
            'dateTimeZone' => $this->dateTimeZone->getName(),
            'serverCpuLoad' => $this->serverCpuLoad,
            'serverDiskFreeSpace' => $this->serverDiskFreeSpace,
            'serverDiskTotalSpace' => $this->serverDiskTotalSpace,
            'serverMemoryUsage' => $this->serverMemoryUsage,
            'serverMemoryPeakUsage' => $this->serverMemoryPeakUsage,
            'exceptionName' => $this->exceptionName,
            'exceptionFileName' => $this->exceptionFileName,
            'parentExceptionName' => $this->parentExceptionName,
            'parentExceptionFileName' => $this->parentExceptionFileName,
            'apiClientVersion' => $this->apiClientVersion,
            'user' => Streply::$user === null ? null : Streply::$user->toArray(),
            'flag' => $this->flag,
            'dir' => $this->dir,
        ];
    }

    public function toJson(): string
    {
        $data = $this->toArray();
        $data = array_filter($data, function ($value) {
            if (is_array($value)) {
                return empty($value) === false;
            }

            return empty($value) === false;
        });

        return json_encode($data);
    }

    public function getValidationError(): ?string
    {
        // Invalid record type
        if (in_array($this->getType(), CaptureType::all(), true) === false) {
            return sprintf('%s is a invalid type', $this->getType());
        }

        // Level
        if (in_array($this->getLevel(), Level::all(), true) === false) {
            return sprintf('%s is a invalid level', $this->getLevel());
        }

        // Params structure
        if (empty($this->getParams()) === false) {
            foreach ($this->getParams() as $param) {
                if (is_string($param['name']) === false) {
                    return 'Param name wave wrong format (only: STRING)';
                }

                if (
                    is_string($param['value']) === false &&
                    is_int($param['value']) === false &&
                    is_float($param['value']) === false &&
                    is_null($param['value']) === false &&
                    is_bool($param['value']) === false
                ) {
                    return sprintf('Param %s have wrong value (only: NULL, STRING, INT, FLOAT, BOOL type)', $param['name']);
                }
            }
        }

        return null;
    }

    public function importFromProperties(Properties $properties): void
    {
        $collections = array_merge(
            $properties->collection($this->getTraceUniqueId()),
            $properties->collection('event')
        );

        foreach ($collections as $name => $property) {
            if (
                in_array($name, self::ALLOWED_PARAMETERS, true) &&
                property_exists($this, $name)
            ) {
                $this->{$name} = $property['value'];

                if ($property['clearAfterRequest'] === true) {
                    \Streply\Streply::Properties()->delete('event', $name);
                }
            }
        }
    }

    public function isAllowedRequest(): bool
    {
        if ($this->getRequestUri() !== null) {
            if (
                strpos($this->getRequestUri(), '/favicon.') !== false ||
                strpos($this->getRequestUri(), '/fav.ico') !== false ||
                strpos($this->getRequestUri(), 'apple-touch-icon.png') !== false ||
                strpos($this->getRequestUri(), 'apple-touch-icon-precomposed.png') !== false ||
                strpos($this->getRequestUri(), 'sitemap.txt') !== false ||
                strpos($this->getRequestUri(), 'sitemap.xml') !== false ||
                strpos($this->getRequestUri(), 'robots.txt') !== false ||
                strpos($this->getRequestUri(), '/_debugbar') !== false ||
                strpos($this->getRequestUri(), '/_wdt') !== false
            ) {
                return false;
            }
        }

        return true;
    }
}
