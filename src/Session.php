<?php

namespace Streply;

class Session
{
	private const SESSION_ID_COOKIE = 'streamly_session_id';
	private const USER_ID_COOKIE = 'streamly_user_id';

	/**
	 * @return string
	 */
	private static function generateRandomId(): string
	{
		$permittedChars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

		return sha1(
			sprintf(
				'%s.%s.%d',
				Streply::getDsn()->getProjectId(),
				substr(
					str_shuffle($permittedChars),
					0,
					16
				),
				time()
			)
		);
	}

	/**
	 * @return string
	 */
	public static function traceId(): string
	{
		return self::generateRandomId();
	}

	/**
	 * @return string
	 */
	public static function sessionId(): string
	{
		if(isset($_COOKIE[Session::SESSION_ID_COOKIE])) {
			setcookie(Session::SESSION_ID_COOKIE, $_COOKIE[Session::SESSION_ID_COOKIE], time()+3600);

			return $_COOKIE[Session::SESSION_ID_COOKIE];
		}

		$sessionId = self::generateRandomId();

		setcookie(Session::SESSION_ID_COOKIE, $sessionId, time()+3600);

		return $sessionId;
	}

	/**
	 * @return string
	 */
	public static function userId(): string
	{
		if(isset($_COOKIE[Session::USER_ID_COOKIE])) {
			return $_COOKIE[Session::USER_ID_COOKIE];
		}

		$userId = self::generateRandomId();

		setcookie(Session::USER_ID_COOKIE, $userId, time()+3600*24*365*10);

		return $userId;
	}
}
