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
use Streamly\Time;
use Streamly\Entity\Breadcrumb;

class Capture
{
	private const SOURCE_LINE_NUMBERS = 10;

	/**
	 * @param \Throwable $exception
	 * @param array $params
	 * @param string $level
	 * @return void
	 */
	public static function Error(\Throwable $exception, array $params = [], string $level = Level::NORMAL): void
	{
		if(Streamly::isInitialize() === false) {
			\Streamly\Log('Streamly is not initialized');

			throw new NotInitializedException();
		}

		// Create record
		$event = Event::create(CaptureType::TYPE_ERROR, $exception->getMessage(), $params, $level);
		$event->setFile($exception->getFile());
		$event->setLine($exception->getLine());

		// Custom HTTP status code
		if(method_exists($exception, 'getStatusCode')) {
			$exceptionStatusCode = $exception->getStatusCode();

			if(is_int($exceptionStatusCode)) {
				$event->setHttpStatusCode($exceptionStatusCode);
			}
		}

		$reflectionClass = new \ReflectionClass($exception);

		$event->setExceptionName($reflectionClass->getName());
		$event->setExceptionFileName($reflectionClass->getFileName());

		if($reflectionClass->getParentClass() !== false) {
			$event->setParentExceptionName($reflectionClass->getParentClass()->getName());
			$event->setParentExceptionFileName($reflectionClass->getParentClass()->getFileName());
		}

		// Trace
		$event->addTrace(
			$exception->getFile(),
			$exception->getLine(),
			null,
			null,
			[],
			CodeSource::load($exception->getFile(), $exception->getLine(), self::SOURCE_LINE_NUMBERS)
		);

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

		// Push
		Handler::Handle($event);
	}

	/**
	 * @param string $message
	 * @param string|null $channel
	 * @param array $params
	 * @return void
	 */
	public static function Activity(string $message, ?string $channel = null, array $params = []): void
	{
		if(Streamly::isInitialize() === false) {
			\Streamly\Log('Streamly is not initialized');

			throw new NotInitializedException();
		}

		// Create record
		$event = Event::create(CaptureType::TYPE_ACTIVITY, $message, $params);
		$event->setChannel($channel);

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
