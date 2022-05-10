<?php

namespace Streamly\Entity;

interface EntityInterface
{
	/**
	 * @return string
	 */
	public function getTraceId(): string;

	/**
	 * @return string
	 */
	public function toJson(): string;

	/**
	 * @return string|null
	 */
	public function getValidationError(): ?string;

	/**
	 * @return bool
	 */
	public function isAllowedRequest(): bool;
}