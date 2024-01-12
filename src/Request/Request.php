<?php

namespace Streply\Request;

use Streply\Logs\Logs;
use Streply\Streply;

class Request
{
    public static function execute(string $input): Response
    {
        $url = Streply::getDsn()->getApiUrl();
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, sprintf('Streply PHP %s', Streply::API_VERSION));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                sprintf('Token: %s', Streply::getDsn()->getPublicKey()),
                sprintf('ProjectId: %s', Streply::getDsn()->getProjectId()),
                'Content-Type: application/json',
                'Content-Length: ' . strlen($input),
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
