<?php

namespace Streply\Capture;

use Streply\Enum\Level;
use Streply\Enum\CaptureType;
use Streply\Streply;
use Streply\Entity\Event;
use Streply\Request\Response;
use Streply\Request\Handler;
use Streply\Exceptions\NotInitializedException;
use Streply\CodeSource;
use Streply\Time;
use Streply\Entity\Breadcrumb;
use Streply\Logs\Logs;
use Streply\Responses\Entity;

class Capture
{
	private const SOURCE_LINE_NUMBERS = 10;

	/**
	 * @param \Throwable $exception
	 * @param array $params
	 * @param string $level
	 * @return Entity|null
	 * @throws NotInitializedException
	 * @throws \Streply\Exceptions\StreplyException
	 */
	public static function Error(\Throwable $exception, array $params = [], string $level = Level::NORMAL): ?Entity
	{
		if(true === Streply::isInitialize()) {
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

			return new Entity($event->getTraceUniqueId());
		}

		Logs::Log('Streply is not initialized');

		return null;
	}

	/**
	 * @param string $message
	 * @param array $params
	 * @param string|null $channel
	 * @return Entity|null
	 * @throws NotInitializedException
	 * @throws \Streply\Exceptions\StreplyException
	 */
	public static function Activity(string $message, array $params = [], ?string $channel = null): ?Entity
	{
		if(true === Streply::isInitialize()) {
			// Create record
			$event = Event::create(CaptureType::TYPE_ACTIVITY, $message, $params);
			$event->setChannel($channel);

			// Push
			Handler::Handle($event);

			return new Entity($event->getTraceUniqueId());
		}

		Logs::Log('Streply is not initialized');

		return null;
	}

	/**
	 * @param string $message
	 * @param array $params
	 * @param string|null $channel
	 * @param string $level
	 * @return Entity|null
	 */
	public static function Log(string $message, array $params = [], ?string $channel = null, string $level = Level::NORMAL): ?Entity
	{
		if(true === Streply::isInitialize()) {
			// Create record
			$event = Event::create(CaptureType::TYPE_LOG, $message, $params, $level);
			$event->setChannel($channel);

			// Push
			Handler::Handle($event);

			return new Entity($event->getTraceUniqueId());
		}

		Logs::Log('Streply is not initialized');

		return null;
	}

	/**
	 * @param string $type
	 * @param string $message
	 * @param array $params
	 * @return void
	 * @throws NotInitializedException
	 * @throws \Streply\Exceptions\InvalidBreadcrumbTypeException
	 * @throws \Streply\Exceptions\StreplyException
	 */
	public static function Breadcrumb(string $type, string $message, array $params = []): void
	{
		if(true === Streply::isInitialize()) {
			$breadcrumb = new Breadcrumb(
				Streply::traceId(),
				Streply::traceUniqueId(),
				$type,
				$message,
				$params
			);

			// Push
			Handler::Handle($breadcrumb);
		}
	}
}
