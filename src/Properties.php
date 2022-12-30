<?php

namespace Streply;

/**
 *
 */
class Properties
{
	/**
	 * @var array
	 */
	private array $parameters = [];

	/**
	 * @param string $collectionName
	 * @return array
	 */
	public function collection(string $collectionName): array
	{
		return $this->parameters[$collectionName] ?? [];
	}

	/**
	 * @param string $collectionName
	 * @param string $name
	 * @return bool
	 */
	public function has(string $collectionName, string $name): bool
	{
		return isset($this->parameters[$collectionName][$name]);
	}

	/**
	 * @param string $collectionName
	 * @param string $name
	 * @param $value
	 * @return void
	 */
	public function set(string $collectionName, string $name, $value): void
	{
		$this->parameters[$collectionName][$name] = $value;
	}

	/**
	 * @param string $collectionName
	 * @param string $name
	 * @param $default
	 * @return mixed|null
	 */
	public function get(string $collectionName, string $name, $default = null)
	{
		if($this->has($collectionName, $name) === false) {
			return $default;
		}

		return $this->parameters[$collectionName][$name];
	}

	/**
	 * @param string $name
	 * @param $value
	 * @return void
	 */
	public function setForPerformance(string $name, $value): void
	{
		$this->set('performance', $name, $value);
	}

	/**
	 * @param string $name
	 * @param $value
	 * @return void
	 */
	public function setForEvent(string $name, $value): void
	{
		$this->set('event', $name, $value);
	}
}