<?php

namespace Streamly\Capture;

use Streamly\Enum\Level;
use Streamly\Enum\CaptureType;
use Streamly\Streamly;
use Streamly\Entity\Record;
use Streamly\Request\Response;
use Streamly\Request\Handler;
use Streamly\Exceptions\NotInitializedException;

class Capture
{
	/**
	 * @param \Exception $exception
	 * @param array $params
	 * @param string $level
	 * @return Response
	 */
	public static function Error(\Exception $exception, array $params = [], string $level = Level::NORMAL): Response
	{
		if(Streamly::isInitialize() === false) {
			\Streamly\Logs\Log('Streamly is not initialized');

			throw new NotInitializedException();
		}

		// Create record
		$record = Record::create(CaptureType::TYPE_ERROR, $exception->getMessage(), $params, $level);
		$record->setFile($exception->getFile());
		$record->setLine($exception->getLine());

		$reflectionClass = new \ReflectionClass($exception);

		$record->setExceptionName($reflectionClass->getName());
		$record->setExceptionFileName($reflectionClass->getFileName());

		if($reflectionClass->getParentClass() !== false) {
			$record->setParentExceptionName($reflectionClass->getParentClass()->getName());
			$record->setParentExceptionFileName($reflectionClass->getParentClass()->getFileName());
		}

		// Trace
		foreach($exception->getTrace() as $trace) {
			$record->addTrace(
				$trace['file'] ?? null,
				$trace['line'] ?? null,
				$trace['function'] ?? null,
				$trace['class'] ?? null,
				$trace['args'] ?? null,
			);
		}

		// Push
		return Handler::Push($record);
	}

	/**
	 * @param string $message
	 * @param array $params
	 * @param string|null $channel
	 * @param string $level
	 * @return Response
	 */
	public static function Message(string $message, array $params = [], ?string $channel = null, string $level = Level::NORMAL): Response
	{
		if(Streamly::isInitialize() === false) {
			\Streamly\Logs\Log('Streamly is not initialized');

			throw new NotInitializedException();
		}

		// Create record
		$record = Record::create(CaptureType::TYPE_MESSAGE, $message, $params, $level);
		$record->setChannel($channel);

		// Push
		return Handler::Push($record);
	}

	/**
	 * @param string $recordId
	 * @param string $channel
	 * @param array $params
	 * @return Response
	 */
	public static function Activity(string $recordId, string $channel, array $params = []): Response
	{
		if(Streamly::isInitialize() === false) {
			\Streamly\Logs\Log('Streamly is not initialized');

			throw new NotInitializedException();
		}

		// Create record
		$record = Record::create(CaptureType::TYPE_ACTIVITY, $recordId, $params);
		$record->setChannel($channel);

		// Push
		return Handler::Push($record);
	}
}
