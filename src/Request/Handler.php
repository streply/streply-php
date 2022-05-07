<?php

namespace Streamly\Request;

use Streamly\Entity\Event;
use Streamly\Exception\InvalidRequestException;
use Streamly\Streamly;

class Handler
{
	/**
	 * @param Event $event
	 * @return Response
	 */
	public static function Push(Event $event): Response
	{
		$validator = new Validator();

		if($validator->isValid($event) === false) {
			throw new InvalidRequestException($validator->output());
		}

		// Create log
		\Streamly\Log(
			sprintf(
				'Capture type:%s, message:%s, level:%s',
				$event->getType(),
				$event->getMessage(),
				$event->getLevel(),
			)
		);

		// Send request
		$url = Streamly::getDsn()->getApiUrl();
		$ch = curl_init($url);
		$input = $event->toJson();

		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER,
			[
				'Content-Type:application/json',
				'Content-Length: ' . strlen($input)
			]
		);

		$result = curl_exec($ch);
		curl_close($ch);

		return new Response($result);
	}
}
