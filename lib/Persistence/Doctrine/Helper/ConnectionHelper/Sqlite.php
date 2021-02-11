<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper;

use Doctrine\DBAL\Connection;
use Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelperInterface;

final class Sqlite implements ConnectionHelperInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function nextId(string $table, string $column = 'id'): int
    {
        $query = $this->connection->createQueryBuilder();
        $query->select($this->connection->getDatabasePlatform()->getMaxExpression($column) . ' AS id')
            ->from($table);

        $data = $query->execute()->fetchAllAssociative();

        return (int) ($data[0]['id'] ?? 0) + 1;
    }

    public function lastId(string $table, string $column = 'id'): int
    {
        $query = $this->connection->createQueryBuilder();
        $query->select($this->connection->getDatabasePlatform()->getMaxExpression($column) . ' AS id')
            ->from($table);

        $data = $query->execute()->fetchAllAssociative();

        return (int) ($data[0]['id'] ?? 0);
    }
}
