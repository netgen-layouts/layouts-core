<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelperInterface;

final class Sqlite implements ConnectionHelperInterface
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function nextId(string $table, string $column = 'id')
    {
        $query = $this->connection->createQueryBuilder();
        $query->select($this->connection->getDatabasePlatform()->getMaxExpression($column) . ' AS id')
            ->from($table);

        $data = $query->execute()->fetchAll(FetchMode::ASSOCIATIVE);

        return (int) ($data[0]['id'] ?? 0) + 1;
    }

    public function lastId(string $table, string $column = 'id')
    {
        $query = $this->connection->createQueryBuilder();
        $query->select($this->connection->getDatabasePlatform()->getMaxExpression($column) . ' AS id')
            ->from($table);

        $data = $query->execute()->fetchAll(FetchMode::ASSOCIATIVE);

        return (int) ($data[0]['id'] ?? 0);
    }
}
