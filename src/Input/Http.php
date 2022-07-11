<?php

namespace Streply\Input;

class Http
{
	private const DEFAULT_PORT = 80;
	private const DEFAULT_SCHEME = 'http';
	private const DEFAULT_METHOD = 'GET';

	/**
	 * @var array
	 */
	private array $request;

	/**
	 * @param array $request
	 */
	public function __construct(array $request)
	{
		$this->request = $request;
	}

	/**
	 * @param string $name
	 * @param string|null $default
	 * @return mixed
	 */
	private function get(string $name, ?string $default = null)
	{
		return $this->request[$name] ?? $default;
	}

	/**
	 * @return string|null
	 */
	public function getHost(): ?string
	{
		return $this->get('HTTP_HOST');
	}

	/**
	 * @return string
	 */
	public function getUserAgent(): string
	{
		$userAgent = $this->get('HTTP_USER_AGENT');

		if($userAgent !== null) {
			return $userAgent;
		}

		$phpSelf = $this->get('PHP_SELF');

		if($phpSelf !== null) {
			return $phpSelf;
		}

		return '';
	}

	/**
	 * @return string|null
	 */
	public function getLanguage(): ?string
	{
		return $this->get('HTTP_ACCEPT_LANGUAGE');
	}

	/**
	 * @return string|null
	 */
	public function getServerName(): ?string
	{
		return $this->get('SERVER_NAME');
	}

	/**
	 * @return string|null
	 */
	public function getServerSoftware(): ?string
	{
		return $this->get('SERVER_SOFTWARE');
	}

	/**
	 * @return int
	 */
	public function getPort(): int
	{
		return (int) $this->get('SERVER_PORT', Http::DEFAULT_PORT);
	}

	/**
	 * @return string
	 */
	public function getScheme(): string
	{
		return $this->get('REQUEST_SCHEME', Http::DEFAULT_SCHEME);
	}

	/**
	 * @return string
	 */
	public function getMethod(): string
	{
		return strtoupper($this->get('REQUEST_METHOD', Http::DEFAULT_METHOD));
	}
	/**
	 * @return string|null
	 */
	public function getUri(): ?string
	{
		return $this->get('REQUEST_URI');
	}

	/**
	 * @return int|null
	 */
	public function getTime(): ?int
	{
		return $this->get('REQUEST_TIME');
	}

	/**
	 * @return float|null
	 */
	public function getTimeFloat(): ?float
	{
		return $this->get('REQUEST_TIME_FLOAT');
	}

	/**
	 * @return string|null
	 */
	public function getIp(): ?string
	{
		return $this->get('SERVER_ADDR');
	}

	/**
	 * @return int
	 */
	public function getStatusCode(): int
	{
		return http_response_code();
	}

	/**
	 * @return string|null
	 */
	public function getContentType(): ?string
	{
		return $this->get('CONTENT_TYPE');
	}

	/**
	 * @return array
	 */
	public function getHeaders(): array
	{
		return getallheaders();
	}

	/**
	 * @return array|null
	 */
	public function getRequestParams(): ?array
	{
		$requestParams = @json_decode(file_get_contents('php://input'), true);

		// Raw body
		if(is_array($requestParams) && empty($requestParams) === false) {
			return $requestParams;
		}

		// POST
		if($this->getMethod() === 'POST' && is_array($_POST)) {
			return $_POST;
		}

		// GET
		if($this->getMethod() === 'GET' && is_array($_GET)) {
			return $_GET;
		}

		return null;
	}

	/**
	 * @return string|null
	 */
	public function getUrl(): ?string
	{
		if(isset($_SERVER['HTTP_HOST']) === false) {
			return null;
		}

		return sprintf(
			'%s://%s/%s',
			isset($_SERVER['HTTPS']) ? 'https' : 'http',
			$_SERVER['HTTP_HOST'],
			trim($_SERVER['REQUEST_URI'],'/\\')
		);
	}
}
