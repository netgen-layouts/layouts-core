<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Doctrine\Helper;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper\PostgreSQL;
use Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper\SQLite;

use function array_find;

final class ConnectionHelper implements ConnectionHelperInterface
{
    /**
     * @var array<class-string, \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelperInterface>
     */
    private array $databaseSpecificHelpers;

    public function __construct(
        private Connection $connection,
    ) {
        $this->databaseSpecificHelpers = [
            SQLitePlatform::class => new SQLite($this->connection),
            PostgreSQLPlatform::class => new PostgreSQL($this->connection),
        ];
    }

    public function nextId(string $table, string $column = 'id'): string
    {
        $handler = $this->getHandler($this->connection->getDatabasePlatform());

        if ($handler !== null) {
            return $handler->nextId($table, $column);
        }

        return 'null';
    }

    public function lastId(string $table, string $column = 'id'): int
    {
        $handler = $this->getHandler($this->connection->getDatabasePlatform());

        if ($handler !== null) {
            return $handler->lastId($table, $column);
        }

        return (int) $this->connection->lastInsertId();
    }

    private function getHandler(AbstractPlatform $platform): ?ConnectionHelperInterface
    {
        return array_find(
            $this->databaseSpecificHelpers,
            static fn (ConnectionHelperInterface $helper, string $class): bool => $platform instanceof $class,
        );
    }
}
