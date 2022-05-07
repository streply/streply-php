<?php

namespace Streamly\Capture;

use Streamly\Enum\Level;
use Streamly\Enum\CaptureType;
use Streamly\Streamly;
use Streamly\Entity\Event;
use Streamly\Request\Response;
use Streamly\Request\Handler;
use Streamly\Exceptions\NotInitializedException;
use Streamly\CodeSource;

class Capture
{
	private const SOURCE_LINE_NUMBERS = 30;

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
		$event = Event::create(CaptureType::TYPE_ERROR, $exception->getMessage(), $params, $level);
		$event->setFile($exception->getFile());
		$event->setLine($exception->getLine());

		$reflectionClass = new \ReflectionClass($exception);

		$event->setExceptionName($reflectionClass->getName());
		$event->setExceptionFileName($reflectionClass->getFileName());

		if($reflectionClass->getParentClass() !== false) {
			$event->setParentExceptionName($reflectionClass->getParentClass()->getName());
			$event->setParentExceptionFileName($reflectionClass->getParentClass()->getFileName());
		}

		// Trace
		foreach($exception->getTrace() as $trace) {
			if(isset($trace['file'], $trace['line'])) {
				$event->addTrace(
					$trace['file'],
					$trace['line'],
					$trace['function'] ?? null,
					$trace['class'] ?? null,
					$trace['args'] ?? [],
					CodeSource::load($trace['file'], $trace['line'], self::SOURCE_LINE_NUMBERS)
				);
			}
		}

		// Debug back trace
		foreach(debug_backtrace() as $debugBackTrace) {
			$event->addDebugBackTrace(
				$debugBackTrace['file'],
				$debugBackTrace['line'],
				$debugBackTrace['function'] ?? null,
				$debugBackTrace['class'] ?? null,
				$debugBackTrace['type'] ?? null,
				$debugBackTrace['args'] ?? [],
				CodeSource::load($debugBackTrace['file'], $debugBackTrace['line'], self::SOURCE_LINE_NUMBERS)
			);
		}

		// Push
		return Handler::Push($event);
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
		$event = Event::create(CaptureType::TYPE_MESSAGE, $message, $params, $level);
		$event->setChannel($channel);

		// Debug back trace
		foreach(debug_backtrace() as $debugBackTrace) {
			$event->addDebugBackTrace(
				$debugBackTrace['file'],
				$debugBackTrace['line'],
				$debugBackTrace['function'] ?? null,
				$debugBackTrace['class'] ?? null,
				$debugBackTrace['type'] ?? null,
				$debugBackTrace['args'] ?? [],
				CodeSource::load($debugBackTrace['file'], $debugBackTrace['line'], self::SOURCE_LINE_NUMBERS)
			);
		}

		// Push
		return Handler::Push($event);
	}

	/**
	 * @param string $recordId
	 * @param string|null $channel
	 * @param array $params
	 * @return Response
	 */
	public static function Activity(string $recordId, ?string $channel = null, array $params = []): Response
	{
		if(Streamly::isInitialize() === false) {
			\Streamly\Logs\Log('Streamly is not initialized');

			throw new NotInitializedException();
		}

		// Create record
		$event = Event::create(CaptureType::TYPE_ACTIVITY, $recordId, $params);
		$event->setChannel($channel);

		// Debug back trace
		foreach(debug_backtrace() as $debugBackTrace) {
			$event->addDebugBackTrace(
				$debugBackTrace['file'],
				$debugBackTrace['line'],
				$debugBackTrace['function'] ?? null,
				$debugBackTrace['class'] ?? null,
				$debugBackTrace['type'] ?? null,
				$debugBackTrace['args'] ?? [],
				CodeSource::load($debugBackTrace['file'], $debugBackTrace['line'], self::SOURCE_LINE_NUMBERS)
			);
		}

		// Push
		return Handler::Push($event);
	}
}
