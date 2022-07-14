<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Doctrine\Helper;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper\Postgres;
use Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper\Sqlite;

use function is_a;

final class ConnectionHelper implements ConnectionHelperInterface
{
    private Connection $connection;

    /**
     * @var array<class-string, \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelperInterface>
     */
    private array $databaseSpecificHelpers;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;

        $this->databaseSpecificHelpers = [
            SqlitePlatform::class => new Sqlite($this->connection),
            PostgreSQLPlatform::class => new Postgres($this->connection),
        ];
    }

    public function nextId(string $table, string $column = 'id')
    {
        $handler = $this->getHandler($this->connection->getDatabasePlatform());

        if ($handler !== null) {
            return $handler->nextId($table, $column);
        }

        return 'null';
    }

    public function lastId(string $table, string $column = 'id')
    {
        $handler = $this->getHandler($this->connection->getDatabasePlatform());

        if ($handler !== null) {
            return $handler->lastId($table, $column);
        }

        return $this->connection->lastInsertId($table);
    }

    private function getHandler(AbstractPlatform $platform): ?ConnectionHelperInterface
    {
        foreach ($this->databaseSpecificHelpers as $class => $helper) {
            if (is_a($platform, $class)) {
                return $helper;
            }
        }

        return null;
    }
}
