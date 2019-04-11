<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Service;

interface Service
{
    /**
     * Runs the provided callable inside a transaction.
     *
     * @param callable $callable
     *
     * @return mixed
     */
    public function transaction(callable $callable);

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
