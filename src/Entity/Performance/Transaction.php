<?php

namespace Streply\Entity\Performance;

use Streply\Streply;
use Streply\Entity\EntityInterface;
use Streply\Time;

/**
 *
 */
class Transaction implements EntityInterface
{
	private string $id;
	private string $name;
	private string $dateTimeZone;
	private float $startTime;
	private ?float $finishTime;
	private string $startDate;
	private ?string $finishDate;
	private string $sessionId;
	private string $userId;
	private ?string $environment;
	private ?string $release;
	private int $projectId;
	private string $traceId;
	private string $traceUniqueId;
	private ?string $url;
	private string $apiClientVersion;
	private ?int $httpStatusCode;
	private ?string $requestUserAgent;
	private ?string $requestServer;
	private ?int $requestPort;
	private ?string $requestScheme;
	private string $requestMethod;
	private ?string $requestUri;
	private ?int $requestTime;
	private ?float $requestTimeFloat;
	private ?string $requestContentType;
	private ?string $file;
	private ?int $line;
	private array $points = [];

	/**
	 * @param string $id
	 * @param string $name
	 * @param string|null $file
	 * @param int|null $line
	 */
	public function __construct(
		string $id,
		string $name,
		?string $file,
		?int $line
	)
	{
		$options = Streply::getOptions();
		$dsn = Streply::getDsn();
		$http = Streply::getHttp();

		$now = new \DateTime();

		$this->id = $id;
		$this->name = $name;
		$this->dateTimeZone = $now->getTimezone()->getName();
		$this->startTime = Time::loadTime();
		$this->startDate = $now->format('Y-m-d H:i:s');
		$this->sessionId = Streply::sessionId();
		$this->userId = Streply::userId();
		$this->environment = $options->get('environment', null);
		$this->release = $options->get('release', null);
		$this->projectId = $dsn->getProjectId();
		$this->traceId = Streply::traceId();
		$this->traceUniqueId = Streply::traceUniqueId();
		$this->url = $http->getUrl();
		$this->apiClientVersion = Streply::API_VERSION;
		$this->httpStatusCode = $http->getStatusCode();

		$this->requestUserAgent = $http->getUserAgent();
		$this->requestServer = $http->getServerSoftware();
		$this->requestPort = $http->getPort();
		$this->requestScheme = $http->getScheme();
		$this->requestMethod = $http->getMethod();
		$this->requestUri = $http->getUri();
		$this->requestTime = $http->getTime();
		$this->requestTimeFloat = $http->getTimeFloat();
		$this->requestContentType = $http->getContentType();

		$this->file = $file;
		$this->line = $line;
	}

	/**
	 * @param Point $point
	 * @return void
	 */
	public function addPoint(Point $point): void
	{
		$this->points[] = $point;
	}

	/**
	 * @return void
	 */
	public function setFinishTime(): void
	{
		$this->finishTime = Time::loadTime();
		$this->finishDate = date('Y-m-d H:i:s');
	}

	/**
	 * @return string
	 */
	public function getTraceId(): string
	{
		return 'undefined';
	}

	/**
	 * @return string
	 */
	public function toJson(): string
	{
		$output = [
			'eventType' => 'performance',
			'id' => $this->id,
			'name' => $this->name,
			'dateTimeZone' => $this->dateTimeZone,
			'startTime' => $this->startTime,
			'finishTime' => $this->finishTime,
			'startDate' => $this->startDate,
			'finishDate' => $this->finishDate,
			'sessionId' => $this->sessionId,
			'userId' => $this->userId,
			'environment' => $this->environment,
			'release' => $this->release,
			'projectId' => $this->projectId,
			'traceId' => $this->traceId,
			'traceUniqueId' => $this->traceUniqueId,
			'url' => $this->url,
			'apiClientVersion' => $this->apiClientVersion,
			'httpStatusCode' => $this->httpStatusCode,
			'requestUserAgent' => $this->requestUserAgent,
			'requestServer' => $this->requestServer,
			'requestPort' => $this->requestPort,
			'requestScheme' => $this->requestScheme,
			'requestMethod' => $this->requestMethod,
			'requestUri' => $this->requestUri,
			'requestTime' => $this->requestTime,
			'requestTimeFloat' => $this->requestTimeFloat,
			'requestContentType' => $this->requestContentType,
			'file' => $this->file,
			'line' => $this->line,
			'points' => [],
			'user' => Streply::$user === null ? null : Streply::$user->toArray()
		];

		foreach($this->points as $point) {
			$output['points'][] = $point->toJson();
		}

		return json_encode($output);
	}

	/**
	 * @return string|null
	 */
	public function getValidationError(): ?string
	{
		// Params structure
		foreach($this->points as $point) {
			foreach($point->params() as $name => $value) {
				if(is_string($name) === false) {
					return 'Param name wave wrong format (only: STRING)';
				}

				if(
					is_string($value) === false &&
					is_int($value) === false &&
					is_float($value) === false &&
					is_null($value) === false
				) {
					return sprintf(
						'Param %s in point %s have wrong value (only: NULL, STRING, INT, FLOAT type)',
						$name,
						$point->name()
					);
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
		return true;
	}
}
