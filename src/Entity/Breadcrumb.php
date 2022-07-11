<?php

namespace Streply\Entity;

use Streply\Enum\BreadcrumbType;
use Streply\Exceptions\InvalidBreadcrumbTypeException;

class Breadcrumb implements EntityInterface
{
	/**
	 * @var string
	 */
	private string $traceId;

	/**
	 * @var string
	 */
	private string $traceUniqueId;

	/**
	 * @var string
	 */
	private string $type;

	/**
	 * @var string
	 */
	private string $message;

	/**
	 * @var array
	 */
	private array $params;

	/**
	 * @var \DateTime
	 */
	private \DateTime $date;

	/**
	 * @var float
	 */
	private float $time;

	/**
	 * @param string $traceId
	 * @param string $traceUniqueId
	 * @param string $type
	 * @param string $message
	 * @param array $params
	 */
	public function __construct(string $traceId, string $traceUniqueId, string $type, string $message, array $params = [])
	{
		if(in_array($type, BreadcrumbType::all(), true) === false) {
			throw new InvalidBreadcrumbTypeException(
				sprintf(
					'%s is a invalid breadcrumb type',
					$type
				)
			);
		}

		$this->traceId = $traceId;
		$this->traceUniqueId = $traceUniqueId;
		$this->type = $type;
		$this->message = $message;
		$this->params = $params;
		$this->date = new \DateTime();
		$this->time = microtime(true);
	}

	/**
	 * @return string
	 */
	public function getTraceId(): string
	{
		return $this->traceId;
	}

	/**
	 * @return string
	 */
	public function toJson(): string
	{
		return json_encode([
			'eventType' => 'breadcrumb',
			'traceUniqueId' => $this->traceUniqueId,
			'type' => $this->type,
			'message' => $this->message,
			'params' => $this->params,
			'date' => $this->date->format('Y-m-d H:i:s'),
			'time' => $this->time
		]);
	}

	/**
	 * @return string|null
	 */
	public function getValidationError(): ?string
	{
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
