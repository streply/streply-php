<?php

namespace Streamly\Request;

class Response
{
	private const STATUS_SUCCESS = 'success';
	private const STATUS_ERROR = 'error';

	/**
	 * @var array
	 */
	private array $response;

	/**
	 * @param string $response
	 */
	public function __construct(string $response)
	{
		$decode = @json_decode($response, true);
		$this->response = is_array($decode) ? $decode : [];
	}

	/**
	 * @return bool
	 */
	public function isSuccess(): bool
	{
		return isset($this->response['status']) && $this->response['status'] === self::STATUS_SUCCESS;
	}

	/**
	 * @return string|null
	 */
	public function getError(): ?string
	{
		return $this->response['data']['message'] ?? null;
	}

	/**
	 * @return array
	 */
	public function getOutput(): array
	{
		return $this->response['data'] ?? [];
	}
}
