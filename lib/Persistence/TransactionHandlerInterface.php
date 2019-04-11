<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence;

interface TransactionHandlerInterface
{
    /**
     * Begins the transaction.
     */
    public function beginTransaction(): void;

    /**
     * Commits the transaction.
     */
    public function commitTransaction(): void;

    /**
     * Rollbacks the transaction.
     */
    public function rollbackTransaction(): void;
}
