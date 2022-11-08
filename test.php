<?php

class Test
{
	public function __construct()
	{
		Streply\Performance::Point('someTransactionId', '__construct');
	}

	public function execute()
	{
		Streply\Performance::Point('someTransactionId', 'execute');
	}
}