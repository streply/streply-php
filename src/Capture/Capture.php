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
use Streamly\Entity\Breadcrumb;

class Capture
{
	private const SOURCE_LINE_NUMBERS = 30;

	/**
	 * @param \Exception $exception
	 * @param array $params
	 * @param string $level
	 * @return void
	 */
	public static function Error(\Exception $exception, array $params = [], string $level = Level::NORMAL): void
	{
		if(Streamly::isInitialize() === false) {
			\Streamly\Log('Streamly is not initialized');

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
		Handler::Handle($event);
	}

	/**
	 * @param string $message
	 * @param array $params
	 * @param string|null $channel
	 * @param string $level
	 * @return void
	 */
	public static function Message(string $message, array $params = [], ?string $channel = null, string $level = Level::NORMAL): void
	{
		if(Streamly::isInitialize() === false) {
			\Streamly\Log('Streamly is not initialized');

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
		Handler::Handle($event);
	}

	/**
	 * @param string $recordId
	 * @param string|null $channel
	 * @param array $params
	 * @return void
	 */
	public static function Activity(string $recordId, ?string $channel = null, array $params = []): void
	{
		if(Streamly::isInitialize() === false) {
			\Streamly\Log('Streamly is not initialized');

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
		Handler::Handle($event);
	}

	/**
	 * @param string $type
	 * @param string $message
	 * @param array $params
	 * @return void
	 */
	public static function Breadcrumb(string $type, string $message, array $params = []): void
	{
		if(Streamly::isInitialize() === false) {
			\Streamly\Log('Streamly is not initialized');

			throw new NotInitializedException();
		}

		$breadcrumb = new Breadcrumb(
			Streamly::traceId(),
			Streamly::traceUniqueId(),
			$type,
			$message,
			$params
		);

		// Push
		Handler::Handle($breadcrumb);
	}
}
