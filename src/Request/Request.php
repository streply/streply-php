<?php

namespace Streamly\Request;

use Streamly\Streamly;
use Streamly\Logs\Logs;

class Request
{
	/**
	 * @param string $input
	 * @return Response
	 */
	public static function execute(string $input): Response
	{
		$url = Streamly::getDsn()->getApiUrl();
		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER,
			[
				sprintf('Token: %s', Streamly::getDsn()->getPublicKey()),
				sprintf('ProjectId: %s', Streamly::getDsn()->getProjectId()),
				'Content-Type: application/json',
				'Content-Length: ' . strlen($input)
			]
		);

		$result = curl_exec($ch);
		curl_close($ch);

		// Add to log
		Logs::Log(
			'INPUT : ' . $input,
			'OUTPUT : ' . $result
		);

		return new Response($result);
	}
}