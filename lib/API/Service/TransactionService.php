<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Service;

interface TransactionService
{
    /**
     * Runs the provided callable inside a transaction.
     */
    public function transaction(callable $callable): mixed;

    /**
     * Begins a transaction.
     */
    public function beginTransaction(): void;

    /**
     * Commits the transaction.
     *
     * @throws \Netgen\Layouts\Exception\RuntimeException If no transaction has been started
     */
    public function commitTransaction(): void;

    /**
     * Rollbacks the transaction.
     *
     * @throws \Netgen\Layouts\Exception\RuntimeException If no transaction has been started
     */
    public function rollbackTransaction(): void;
}
