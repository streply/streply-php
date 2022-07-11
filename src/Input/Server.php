<?php

namespace Streply\Input;

class Server
{
	/**
	 * @return float|null
	 */
	public function getCpuLoad(): ?float
	{
		$load = sys_getloadavg();

		return $load[0] ?? null;
	}

	/**
	 * @return float|null
	 */
	public function getDiskFreeSpace(): ?float
	{
		$value = disk_free_space('/');

		return $value === false ? null : $value;
	}

	/**
	 * @return float|null
	 */
	public function getDiskTotalSpace(): ?float
	{
		$value = disk_total_space('/');

		return $value === false ? null : $value;
	}

	/**
	 * @return int
	 */
	public function getMemoryUsage(): int
	{
		return memory_get_usage();
	}

	/**
	 * @return int
	 */
	public function getMemoryPeakUsage(): int
	{
		return memory_get_peak_usage();
	}
}
