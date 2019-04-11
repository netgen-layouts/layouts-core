<?php

declare(strict_types=1);

namespace Netgen\Layouts\Core\Service;

use Netgen\Layouts\API\Service\Service as APIService;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Persistence\TransactionHandlerInterface;
use Throwable;

abstract class Service implements APIService
{
    /**
     * @var \Netgen\Layouts\Persistence\TransactionHandlerInterface
     */
    private $transactionHandler;

    public function __construct(TransactionHandlerInterface $transactionHandler)
    {
        $this->transactionHandler = $transactionHandler;
    }

    public function transaction(callable $callable)
    {
        $this->beginTransaction();

        try {
            $return = $callable();
        } catch (Throwable $t) {
            $this->rollbackTransaction();

            throw $t;
        }

        $this->commitTransaction();

        return $return;
    }

    public function beginTransaction(): void
    {
        $this->transactionHandler->beginTransaction();
    }

    public function commitTransaction(): void
    {
        try {
            $this->transactionHandler->commitTransaction();
        } catch (Throwable $t) {
            throw new RuntimeException($t->getMessage(), 0, $t);
        }
    }

    public function rollbackTransaction(): void
    {
        try {
            $this->transactionHandler->rollbackTransaction();
        } catch (Throwable $t) {
            throw new RuntimeException($t->getMessage(), 0, $t);
        }
    }
}
