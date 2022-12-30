<?php

namespace Streply\Performance;

use Streply\Entity\Performance\Transaction;
use Streply\Entity\Performance\Point;
use Streply\Exceptions\TransactionExistsException;
use Streply\Exceptions\WrongTransactionException;
use Streply\Exceptions\InvalidRequestException;
use Streply\Request\Request;
use Streply\Streply;

class Transactions
{
	/**
	 * @var array
	 */
	private array $transactions = [];

	/**
	 * @param string $transactionId
	 * @param string $transactionName
	 * @param string|null $file
	 * @param int|null $line
	 * @return void
	 */
	public function create(
		string $transactionId,
		string $transactionName,
		?string $file,
		?int $line
	): void
	{
		if($this->has($transactionId)) {
			throw new TransactionExistsException();
		}

		$this->transactions[$transactionId] = new Transaction($transactionId, $transactionName, $file, $line);
	}

	/**
	 * @param string $transactionId
	 * @return bool
	 */
	public function has(string $transactionId): bool
	{
		return isset($this->transactions[$transactionId]);
	}

	/**
	 * @param string $transactionId
	 * @param string $pointName
	 * @param array $params
	 * @param string|null $file
	 * @param int|null $line
	 * @return void
	 */
	public function addPoint(
		string $transactionId,
		string $pointName,
		array $params,
		?string $file,
		?int $line
	): void
	{
		if($this->has($transactionId) === false) {
			throw new WrongTransactionException();
		}

		$this->transactions[$transactionId]->addPoint(
			new Point(
				$pointName,
				$params,
				$file,
				$line
			)
		);
	}

	/**
	 * @param string|null $transactionId
	 * @return void
	 */
	public function finish(string $transactionId = null): void
	{
		if($this->has($transactionId) === false) {
			throw new WrongTransactionException();
		}

		// Request is correct
		if($this->transactions[$transactionId]->isAllowedRequest()) {
			$this->transactions[$transactionId]->importFromProperties(Streply::Properties());

			$this->transactions[$transactionId]->setFinishTime();

			// Validation
			$validationError = $this->transactions[$transactionId]->getValidationError();

			if($validationError !== null) {
				throw new InvalidRequestException($validationError);
			}

			// Send request to API
			Request::execute(
				$this->transactions[$transactionId]->toJson()
			);
		}
	}

	/**
	 * @return array
	 */
	public function transactions(): array
	{
		return $this->transactions;
	}
}