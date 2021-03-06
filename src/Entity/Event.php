<?php

namespace Streply\Entity;

use Streply\Session;
use Streply\Input\Options;
use Streply\Input\Dsn;
use Streply\Input\Http;
use Streply\Input\Server;
use Streply\Enum\CaptureType;
use Streply\Enum\Level;
use Streply\Time;
use Streply\Streply;

class Event implements EntityInterface
{
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

	/**
	 * @param string $type
	 * @param string $message
	 * @param array $params
	 * @param string $level
	 * @return Event
	 */
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
	)
	{
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
	}

	/**
	 * @param array $params
	 * @return void
	 */
	public function setParams(array $params): void
	{
		foreach($params as $name => $value) {
			$this->addParam($name, $value);
		}
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public function addParam(string $name, $value): void
	{
		$this->params[] = ['name' => $name, 'value' => $value];
	}

	/**
	 * @return string
	 */
	public function getTraceId(): string
	{
		return $this->traceId;
	}

	/**
	 * @param string $traceId
	 */
	public function setTraceId(string $traceId): void
	{
		$this->traceId = $traceId;
	}

	/**
	 * @return array
	 */
	public function getParams(): array
	{
		return $this->params;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * @param string $type
	 */
	public function setType(string $type): void
	{
		$this->type = $type;
	}

	/**
	 * @return string
	 */
	public function getLevel(): string
	{
		return $this->level;
	}

	/**
	 * @param string $level
	 */
	public function setLevel(string $level): void
	{
		$this->level = $level;
	}

	/**
	 * @return string
	 */
	public function getMessage(): string
	{
		return $this->message;
	}

	/**
	 * @param string $message
	 */
	public function setMessage(string $message): void
	{
		$this->message = $message;
	}

	/**
	 * @return string|null
	 */
	public function getChannel(): ?string
	{
		return $this->channel;
	}

	/**
	 * @param string|null $channel
	 */
	public function setChannel(?string $channel): void
	{
		$this->channel = $channel;
	}

	/**
	 * @return string|null
	 */
	public function getFile(): ?string
	{
		return $this->file;
	}

	/**
	 * @param string|null $file
	 */
	public function setFile(?string $file): void
	{
		$this->file = $file;
	}

	/**
	 * @return int|null
	 */
	public function getLine(): ?int
	{
		return $this->line;
	}

	/**
	 * @param int|null $line
	 */
	public function setLine(?int $line): void
	{
		$this->line = $line;
	}

	/**
	 * @return string|null
	 */
	public function getExceptionName(): ?string
	{
		return $this->exceptionName;
	}

	/**
	 * @param string|null $exceptionName
	 */
	public function setExceptionName(?string $exceptionName): void
	{
		$this->exceptionName = $exceptionName;
	}

	/**
	 * @return string|null
	 */
	public function getExceptionFileName(): ?string
	{
		return $this->exceptionFileName;
	}

	/**
	 * @param string|null $exceptionFileName
	 */
	public function setExceptionFileName(?string $exceptionFileName): void
	{
		$this->exceptionFileName = $exceptionFileName;
	}

	/**
	 * @return string|null
	 */
	public function getParentExceptionName(): ?string
	{
		return $this->parentExceptionName;
	}

	/**
	 * @param string|null $parentExceptionName
	 */
	public function setParentExceptionName(?string $parentExceptionName): void
	{
		$this->parentExceptionName = $parentExceptionName;
	}

	/**
	 * @return string|null
	 */
	public function getParentExceptionFileName(): ?string
	{
		return $this->parentExceptionFileName;
	}

	/**
	 * @param string|null $parentExceptionFileName
	 */
	public function setParentExceptionFileName(?string $parentExceptionFileName): void
	{
		$this->parentExceptionFileName = $parentExceptionFileName;
	}

	/**
	 * @return string|null
	 */
	public function getRequestUri(): ?string
	{
		return $this->requestUri;
	}

	/**
	 * @param int|null $httpStatusCode
	 */
	public function setHttpStatusCode(?int $httpStatusCode): void
	{
		$this->httpStatusCode = $httpStatusCode;
	}

	/**
	 * @param float $loadTime
	 * @return void
	 */
	public function setLoadTime(float $loadTime): void
	{
		$this->loadTime = $loadTime;
	}

	/**
	 * @param string $file
	 * @param int $line
	 * @param string|null $function
	 * @param string|null $class
	 * @param array $args
	 * @param array $source
	 * @return void
	 */
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

	/**
	 * @return array
	 */
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
		];
	}

	/**
	 * @return string
	 */
	public function toJson(): string
	{
		$data = $this->toArray();
		$data = array_filter($data, function($value) {
			if(is_array($value)) {
				return empty($value) === false;
			}

			return empty($value) === false;
		});

		return json_encode($data);
	}

	/**
	 * @return string|null
	 */
	public function getValidationError(): ?string
	{
		// Invalid record type
		if(in_array($this->getType(), CaptureType::all(), true) === false) {
			return sprintf('%s is a invalid type', $this->getType());
		}

		// Level
		if(in_array($this->getLevel(), Level::all(), true) === false) {
			return sprintf('%s is a invalid level', $this->getLevel());
		}

		// Params structure
		if(empty($this->getParams()) === false) {
			foreach($this->getParams() as $param) {
				if(
					is_string($param['value']) === false &&
					is_int($param['value']) === false &&
					is_float($param['value']) === false &&
					is_null($param['value']) === false
				) {
					return sprintf('Param %s have wrong value (only: NULL, STRING, INT, FLOAT type)', $param['name']);
				}
			}
		}

		return null;
	}

	/**
	 * @return bool
	 */
	public function isAllowedRequest(): bool
	{
		if(
			strpos($this->getRequestUri(), '/favicon.') !== false ||
			strpos($this->getRequestUri(), 'apple-touch-icon.png') !== false ||
			strpos($this->getRequestUri(), 'apple-touch-icon-precomposed.png') !== false ||
			strpos($this->getRequestUri(), 'sitemap.txt') !== false ||
			strpos($this->getRequestUri(), 'sitemap.xml') !== false ||
			strpos($this->getRequestUri(), 'robots.txt') !== false ||
			strpos($this->getRequestUri(), '/_wdt') !== false
		) {
			return false;
		}

		return true;
	}
}
