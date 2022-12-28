<?php

namespace Streply;

/**
 *
 */
class ParameterBag
{
	/**
	 * @var array
	 */
	private array $parameters = [];

	/**
	 * @return array
	 */
	public function all(): array
	{
		return $this->parameters;
	}

	/**
	 * @return int
	 */
	public function count(): int
	{
		return count($this->all());
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function has(string $name): bool
	{
		return isset($this->parameters[$name]);
	}

	/**
	 * @param string $name
	 * @param $value
	 * @return void
	 */
	public function set(string $name, $value): void
	{
		$this->parameters[$name] = $value;
	}

	/**
	 * @param string $name
	 * @param $default
	 * @return mixed
	 */
	public function get(string $name, $default = null)
	{
		if($this->has($name) === false) {
			return $default;
		}

		return $this->parameters[$name];
	}
}