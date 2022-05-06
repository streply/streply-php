<?php

namespace Streamly\Entity;

use Streamly\Session;
use Streamly\Input\Options;
use Streamly\Input\Dsn;
use Streamly\Input\Http;
use Streamly\Input\Server;
use Streamly\Enum\Level;
use Streamly\Streamly;

class Record implements EntityInterface
{
	private string $sessionId;
	private string $userId;
	private int $status;
	private string $type;
	private string $projectId;
	private string $message;
	private string $level;
	private \DateTime $date;
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

	private string $requestHost;
	private string $requestUserAgent;
	private string $requestLanguage;
	private string $requestServer;
	private string $requestServerName;
	private int $requestPort;
	private string $requestScheme;
	private string $requestMethod;
	private string $requestUri;
	private ?int $requestTime;
	private ?float $requestTimeFloat;
	private string $requestIp;
	private \DateTimeZone $dateTimeZone;

	private ?float $serverCpuLoad;
	private ?float $serverDiskFreeSpace;
	private ?float $serverDiskTotalSpace;
	private int $serverMemoryUsage;
	private int $serverMemoryPeakUsage;

	private ?string $exceptionName = null;
	private ?string $exceptionFileName = null;
	private ?string $parentExceptionName = null;
	private ?string $parentExceptionFileName = null;

	/**
	 * @param string $type
	 * @param string $message
	 * @param array $params
	 * @param string $level
	 * @return Record
	 */
	public static function create(string $type, string $message, array $params = [], string $level = Level::NORMAL): Record
	{
		$record = new Record(
			Streamly::getOptions(),
			Streamly::getDsn(),
			Streamly::getHttp(),
			Streamly::getServer()
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
		$now = new \DateTime();

		$this->sessionId = Session::sessionId();
		$this->userId = Session::userId();
		$this->status = 0;
		$this->level = \Streamly\Enum\Level::NORMAL;
		$this->date = $now;
		$this->technology = 'php';
		$this->params = [];
		$this->dateTimeZone = $now->getTimezone();
		$this->environment = $options->get('environment', null);
		$this->release = $options->get('release', null);
		$this->projectId = $dsn->getProjectId();
		$this->requestHost = $http->getHost();
		$this->requestUserAgent = $http->getUserAgent();
		$this->requestLanguage = $http->getLanguage();
		$this->requestServerName = $http->getServerName();
		$this->requestServer = $http->getServerSoftware();
		$this->requestPort = $http->getPort();
		$this->requestScheme = $http->getScheme();
		$this->requestMethod = $http->getMethod();
		$this->requestUri = $http->getUri();
		$this->requestTime = $http->getTime();
		$this->requestTimeFloat = $http->getTimeFloat();
		$this->requestIp = $http->getIp();
		$this->httpStatusCode = $http->getStatusCode();
		$this->url = $http->getUrl();
		$this->serverCpuLoad = $server->getCpuLoad();
		$this->serverDiskFreeSpace = $server->getDiskFreeSpace();
		$this->serverDiskTotalSpace = $server->getDiskTotalSpace();
		$this->serverMemoryUsage = $server->getMemoryUsage();
		$this->serverMemoryPeakUsage = $server->getMemoryPeakUsage();
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
			'args' => $args,
			'source' => $source
		];
	}

	/**
	 * @return string
	 */
	public function toJson(): string
	{
		return json_encode([
			'sessionId' => $this->sessionId,
			'userId' => $this->userId,
			'status' => $this->status,
			'type' => $this->type,
			'projectId' => $this->projectId,
			'message' => $this->message,
			'level' => $this->level,
			'date' => $this->date->format('Y-m-d H:i:s'),
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
			'requestHost' => $this->requestHost,
			'requestUserAgent' => $this->requestUserAgent,
			'requestLanguage' => $this->requestLanguage,
			'requestServer' => $this->requestServer,
			'requestServerName' => $this->requestServerName,
			'requestPort' => $this->requestPort,
			'requestScheme' => $this->requestScheme,
			'requestMethod' => $this->requestMethod,
			'requestUri' => $this->requestUri,
			'requestTime' => $this->requestTime,
			'requestTimeFloat' => $this->requestTimeFloat,
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
		]);
	}
}
