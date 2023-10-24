<?php

namespace Streply\Entity;

use Streply\Input\Http;

/**
 *
 */
class User
{
	/**
	 * @var string
	 */
	private string $userId;

	/**
	 * @var string
	 */
	private string $userName;

	/**
	 * @var array
	 */
	private array $params = [];

	/**
	 * @param string $userId
	 * @param string $userName
	 * @param array $params
	 */
	public function __construct(string $userId, string $userName, array $params = [])
	{
		$this->userId = $userId;
		$this->userName = $userName;

		foreach($params as $name => $value) {
			$this->params[] = ['name' => $name, 'value' => $value];
		}
	}

	/**
	 * @return string
	 */
	public function userId(): string
	{
		return $this->userId;
	}

	/**
	 * @return string
	 */
	public function userName(): string
	{
		return $this->userName;
	}

	/**
	 * @return array
	 */
	public function params(): array
	{
		return $this->params;
	}

	/**
	 * @return string|null
	 */
	public function getValidationError(): ?string
	{
		// Params structure
		foreach($this->params() as $param) {
			if(is_string($param['name']) === false) {
				return 'Param name wave wrong format (only: STRING)';
			}

			if(
				is_string($param['value']) === false &&
				is_int($param['value']) === false &&
				is_float($param['value']) === false &&
				is_null($param['value']) === false &&
                is_bool($param['value']) === false
			) {
				return sprintf(
					'User param %s have wrong value (only: NULL, STRING, INT, FLOAT, BOOL type)',
					$param['name']
				);
			}
		}

		return null;
	}

	/**
	 * @return array
	 */
	public function toArray(): array
	{
		return [
			'userId' => $this->userId,
			'userName' => $this->userName,
			'params' => $this->params
		];
	}
}
