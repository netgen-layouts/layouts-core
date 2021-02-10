<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Doctrine\Helper;

use Doctrine\DBAL\Connection;
use Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper\Postgres;
use Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper\Sqlite;

final class ConnectionHelper implements ConnectionHelperInterface
{
    private Connection $connection;

    /**
     * @var array<string, \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelperInterface>
     */
    private array $databaseSpecificHelpers;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;

        $this->databaseSpecificHelpers = [
            'sqlite' => new Sqlite($this->connection),
            'postgresql' => new Postgres($this->connection),
        ];
    }

    public function nextId(string $table, string $column = 'id')
    {
        $databaseServer = $this->connection->getDatabasePlatform()->getName();
        if (isset($this->databaseSpecificHelpers[$databaseServer])) {
            return $this->databaseSpecificHelpers[$databaseServer]
                ->nextId($table, $column);
        }

        return 'null';
    }

    public function lastId(string $table, string $column = 'id')
    {
        $databaseServer = $this->connection->getDatabasePlatform()->getName();
        if (isset($this->databaseSpecificHelpers[$databaseServer])) {
            return $this->databaseSpecificHelpers[$databaseServer]
                ->lastId($table, $column);
        }

        return $this->connection->lastInsertId($table);
    }
}
