<?php

namespace Streply;

/**
 *
 */
class Performance
{
	/**
	 * @param string $transactionId
	 * @param string $transactionName
	 * @return void
	 */
	public static function Start(string $transactionId, string $transactionName): void
	{
		Logs\Logs::Log(sprintf('Start translation #%s', $transactionId));

		$backTrace = debug_backtrace();
		$currentBackTrace = array_shift($backTrace);

		Streply::getPerformanceTransactions()->create(
			$transactionId,
			$transactionName,
			$currentBackTrace['file'] ?? null,
			$currentBackTrace['line'] ?? null,
		);
	}

	/**
	 * @param string $transactionId
	 * @param string $pointName
	 * @param array $params
	 * @return void
	 */
	public static function Point(string $transactionId, string $pointName, array $params = []): void
	{
		Logs\Logs::Log(sprintf('Add points %s to translation %s', $pointName, $transactionId));

		$backTrace = debug_backtrace();
		$currentBackTrace = array_shift($backTrace);

		Streply::getPerformanceTransactions()->addPoint(
			$transactionId,
			$pointName,
			$params,
			$currentBackTrace['file'] ?? null,
			$currentBackTrace['line'] ?? null,
		);
	}

	/**
	 * @param string $transactionId
	 * @return void
	 */
	public static function Finish(string $transactionId): void
	{
		Logs\Logs::Log(sprintf('Finish translations #%s', $transactionId));

		Streply::getPerformanceTransactions()->finish($transactionId);
	}
}
