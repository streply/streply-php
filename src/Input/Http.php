<?php

namespace Streply\Input;

class Http
{
    private const DEFAULT_PORT = 80;

    private const DEFAULT_SCHEME = 'http';

    private const DEFAULT_METHOD = 'GET';

    private array $request;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    /**
     * @return mixed
     */
    private function get(string $name, ?string $default = null)
    {
        return $this->request[$name] ?? $default;
    }

    public function getHost(): ?string
    {
        return $this->get('HTTP_HOST');
    }

    public function getUserAgent(): string
    {
        $userAgent = $this->get('HTTP_USER_AGENT');

        if ($userAgent !== null) {
            return $userAgent;
        }

        $phpSelf = $this->get('PHP_SELF');

        if ($phpSelf !== null) {
            return $phpSelf;
        }

        return '';
    }

    public function getLanguage(): ?string
    {
        return $this->get('HTTP_ACCEPT_LANGUAGE');
    }

    public function getServerName(): ?string
    {
        return $this->get('SERVER_NAME');
    }

    public function getServerSoftware(): ?string
    {
        return $this->get('SERVER_SOFTWARE');
    }

    public function getPort(): int
    {
        return (int) $this->get('SERVER_PORT', Http::DEFAULT_PORT);
    }

    public function getScheme(): string
    {
        return $this->get('REQUEST_SCHEME', Http::DEFAULT_SCHEME);
    }

    public function getMethod(): string
    {
        return strtoupper($this->get('REQUEST_METHOD', Http::DEFAULT_METHOD));
    }

    public function getUri(): ?string
    {
        return $this->get('REQUEST_URI');
    }

    public function getTime(): ?int
    {
        return $this->get('REQUEST_TIME');
    }

    public function getTimeFloat(): ?float
    {
        return $this->get('REQUEST_TIME_FLOAT');
    }

    public function getIp(): ?string
    {
        return $this->get('SERVER_ADDR');
    }

    public function getStatusCode(): int
    {
        return http_response_code();
    }

    public function getContentType(): ?string
    {
        return $this->get('CONTENT_TYPE');
    }

    public function getHeaders(): array
    {
        $headers = [];

        if (isset($_SERVER)) {
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
        }

        return $headers;
    }

    public function getRequestParams(): ?array
    {
        $requestParams = @json_decode(file_get_contents('php://input'), true);

        // Raw body
        if (is_array($requestParams) && empty($requestParams) === false) {
            return $requestParams;
        }

        // POST
        if ($this->getMethod() === 'POST' && is_array($_POST)) {
            return $_POST;
        }

        // GET
        if ($this->getMethod() === 'GET' && is_array($_GET)) {
            return $_GET;
        }

        return null;
    }

    public function getUrl(): ?string
    {
        if (isset($_SERVER['HTTP_HOST']) === false) {
            return null;
        }

        return sprintf(
            '%s://%s/%s',
            isset($_SERVER['HTTPS']) ? 'https' : 'http',
            $_SERVER['HTTP_HOST'],
            trim($_SERVER['REQUEST_URI'], '/\\')
        );
    }
}
