<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper;

use Doctrine\DBAL\Connection;
use Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelperInterface;

final class Postgres implements ConnectionHelperInterface
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getAutoIncrementValue(string $table, string $column = 'id')
    {
        return "nextval('" . $this->connection->getDatabasePlatform()->getIdentitySequenceName($table, $column) . "')";
    }

    public function lastInsertId(string $table, string $column = 'id')
    {
        return $this->connection->lastInsertId(
            $this->connection->getDatabasePlatform()->getIdentitySequenceName($table, $column)
        );
    }
}
