<?php

namespace Streply\Input;

use Streply\Exceptions\InvalidDsnException;

class Dsn
{
    private string $scheme;

    private string $server;

    private int $projectId;

    private string $publicKey;

    private int $port;

    /**
     * @throws InvalidDsnException
     */
    public function __construct(string $dsn)
    {
        $this->createFromString($dsn);
    }

    /**
     * @throws InvalidDsnException
     */
    public function createFromString(string $dsn): void
    {
        $parsedDsn = parse_url($dsn);

        if ($parsedDsn === false) {
            throw new InvalidDsnException(
                sprintf(
                    'The DNS value is invalid (%s)',
                    $dsn
                )
            );
        }

        foreach (['scheme', 'host', 'path', 'user'] as $dsnPart) {
            if (empty($parsedDsn[$dsnPart])) {
                throw new InvalidDsnException(
                    sprintf(
                        'The "%s" DSN must contain a scheme, a host, a user and a path component',
                        $dsnPart
                    )
                );
            }
        }

        if (\in_array($parsedDsn['scheme'], ['http', 'https'], true) === false) {
            throw new InvalidDsnException(
                sprintf(
                    '%s is a invalid scheme',
                    $parsedDsn['scheme']
                )
            );
        }

        $paths = explode('/', substr($parsedDsn['path'], 1));

        if (empty($paths[0])) {
            throw new InvalidDsnException('The project ID is invalid');
        }

        $this->scheme = $parsedDsn['scheme'] ?? 'https';
        $this->server = $parsedDsn['host'];
        $this->port = $parsedDsn['port'] ?? ('http' === $parsedDsn['scheme'] ? 80 : 443);
        $this->publicKey = $parsedDsn['user'];
        $this->projectId = (int) $paths[0];
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getServer(): string
    {
        return $this->server;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function getProjectId(): int
    {
        return $this->projectId;
    }

    public function getApiUrl(): string
    {
        $apiUrl = $this->getScheme() . '://' . $this->getServer();

        // Port is different from 80 or 443
        if (
            ('http' === $this->getScheme() && 80 !== $this->getPort()) ||
            ('https' === $this->getScheme() && 443 !== $this->getPort())
        ) {
            $apiUrl .= ':' . $this->getPort();
        }

        return $apiUrl;
    }
}
