<?php

namespace Streply\Performance;

use Streply\Entity\Performance\Point;
use Streply\Entity\Performance\Transaction;
use Streply\Exceptions\InvalidRequestException;
use Streply\Exceptions\TransactionExistsException;
use Streply\Exceptions\WrongTransactionException;
use Streply\Request\Request;
use Streply\Streply;

class Transactions
{
    private array $transactions = [];

    public function create(
        string $transactionId,
        string $transactionName,
        ?string $file,
        ?int $line
    ): void {
        if ($this->has($transactionId)) {
            throw new TransactionExistsException();
        }

        $this->transactions[$transactionId] = new Transaction($transactionId, $transactionName, $file, $line);
    }

    public function has(string $transactionId): bool
    {
        return isset($this->transactions[$transactionId]);
    }

    public function addPoint(
        string $transactionId,
        string $pointName,
        array $params,
        ?string $file,
        ?int $line
    ): void {
        if ($this->has($transactionId) === false) {
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

    public function finish(string $transactionId = null): void
    {
        if ($this->has($transactionId) === false) {
            throw new WrongTransactionException();
        }

        // Request is correct
        if ($this->transactions[$transactionId]->isAllowedRequest()) {
            $this->transactions[$transactionId]->importFromProperties(Streply::Properties());

            $this->transactions[$transactionId]->setFinishTime();

            // Validation
            $validationError = $this->transactions[$transactionId]->getValidationError();

            if ($validationError !== null) {
                throw new InvalidRequestException($validationError);
            }

            // Send request to API
            Request::execute(
                $this->transactions[$transactionId]->toJson()
            );
        }
    }

    public function transactions(): array
    {
        return $this->transactions;
    }
}
