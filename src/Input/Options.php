<?php

namespace Streply\Input;

class Options
{
	/**
	 * @var array
	 */
	private array $options;

	/**
	 * @param array $options
	 */
	public function __construct(array $options)
	{
		$this->options = $options;
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function has(string $name): bool
	{
		return isset($this->options[$name]);
	}

	/**
	 * @param string $name
	 * @param $default
	 * @return mixed|null
	 */
	public function get(string $name, $default = null)
	{
		return $this->options[$name] ?? $default;
	}

	/**
	 * @param string $name
	 * @param $value
	 * @return void
	 */
	public function set(string $name, $value): void
	{
		$this->options[$name] = $value;
	}
}
