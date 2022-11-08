<?php

namespace Streply\Entity\Performance;

use Streply\Time;

/**
 *
 */
class Point
{
	private string $name;
	private array $params;
	private float $time;
	private string $date;
	private ?string $file;

	private ?int $line;

	/**
	 * @param string $name
	 * @param array $params
	 * @param string|null $file
	 * @param int|null $line
	 */
	public function __construct(
		string $name,
		array $params,
		?string $file,
		?int $line
	)
	{
		$this->name = $name;
		$this->params = $params;
		$this->time = Time::loadTime();
		$this->date = date('Y-m-d H:i:s');
		$this->file = $file;
		$this->line = $line;
	}

	/**
	 * @return string
	 */
	public function toJson(): string
	{
		$params = [];

		foreach($this->params as $name => $value) {
			$params[] = ['name' => $name, 'value' => $value];
		}

		return json_encode([
			'name' => $this->name,
			'params' => $params,
			'time' => $this->time,
			'date' => $this->date,
			'file' => $this->file,
			'line' => $this->line,
		]);
	}

	/**
	 * @return string
	 */
	public function name(): string
	{
		return $this->name;
	}

	/**
	 * @return array
	 */
	public function params(): array
	{
		return $this->params;
	}
}