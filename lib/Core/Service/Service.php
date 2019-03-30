<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Core\Service;

use Netgen\BlockManager\API\Service\Service as APIService;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Persistence\TransactionHandlerInterface;
use Throwable;

abstract class Service implements APIService
{
    /**
     * @var \Netgen\BlockManager\Persistence\TransactionHandlerInterface
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
