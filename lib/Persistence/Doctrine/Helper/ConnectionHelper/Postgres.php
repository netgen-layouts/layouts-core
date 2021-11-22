<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper;

use Doctrine\DBAL\Connection;
use Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelperInterface;

final class Postgres implements ConnectionHelperInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function nextId(string $table, string $column = 'id'): string
    {
        return "nextval('" . $this->connection->getDatabasePlatform()->getIdentitySequenceName($table, $column) . "')";
    }

    public function lastId(string $table, string $column = 'id'): string
    {
        return (string) $this->connection->lastInsertId(
            $this->connection->getDatabasePlatform()->getIdentitySequenceName($table, $column),
        );
    }
}
