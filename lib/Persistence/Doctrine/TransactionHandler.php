<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Doctrine;

use Doctrine\DBAL\Connection;
use Netgen\Layouts\Persistence\TransactionHandlerInterface;

final class TransactionHandler implements TransactionHandlerInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function beginTransaction(): void
    {
        $this->connection->beginTransaction();
    }

    public function commitTransaction(): void
    {
        $this->connection->commit();
    }

    public function rollbackTransaction(): void
    {
        $this->connection->rollBack();
    }
}
