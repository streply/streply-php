<?php

namespace Streply\Store\Providers;

use Streply\Exceptions\StreplyException;
use Streply\Entity\EntityInterface;
use Streply\Store\Providers\RequestProvider;
use Streply\Request\Request;

class FileProvider implements StoreProviderInterface
{
	/**
	 * @var string|false
	 */
	private string $path;

	/**
	 * @param string $path
	 */
	public function __construct(string $path)
	{
		$this->path = $path;

		if(substr($this->path, -1) === '/') {
			$this->path = substr($this->path, 0, -1);
		}

		if(is_dir($this->path) === false) {
			throw new StreplyException(
				sprintf(
					'%s is not a valid folder',$this->path
				)
			);
		}
	}

	/**
	 * @return string
	 */
	public function name(): string
	{
		return 'file';
	}

	/**
	 * @param string $traceId
	 * @return string
	 */
	private function getFileName(string $traceId): string
	{
		return sprintf(
			'%s/%s.dat',
			$this->path,
			$traceId
		);
	}

	/**
	 * @param EntityInterface $event
	 * @return void
	 */
	public function push(EntityInterface $event): void
	{
		$content = '';

		if(is_file($this->getFileName($event->getTraceId()))) {
			$content = file_get_contents(
				$this->getFileName(
					$event->getTraceId()
				)
			);
		}

		file_put_contents(
			$this->getFileName($event->getTraceId()),
			$content . $event->toJson() . "\n"
		);
	}

	/**
	 * @param string $traceId
	 * @return void
	 */
	public function close(string $traceId): void
	{
		if(is_file($this->getFileName($traceId))) {
			$requests = file($this->getFileName($traceId));

			if(is_array($requests)) {
				foreach($requests as $request) {
					$request = trim($request);

					Request::execute($request);
				}
			}

			unlink($this->getFileName($traceId));
		}
	}
}
