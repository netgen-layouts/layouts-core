<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper;

use Doctrine\DBAL\Connection;
use Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelperInterface;

final class SQLite implements ConnectionHelperInterface
{
    public function __construct(
        private Connection $connection,
    ) {}

    public function nextId(string $table, string $column = 'id'): string
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('MAX(' . $column . ') AS id')
            ->from($table);

        $data = $query->fetchAllAssociative();

        return (string) (($data[0]['id'] ?? 0) + 1);
    }

    public function lastId(string $table, string $column = 'id'): int
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('MAX(' . $column . ') AS id')
            ->from($table);

        $data = $query->fetchAllAssociative();

        return (int) ($data[0]['id'] ?? 0);
    }
}
