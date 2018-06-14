<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper;

use Doctrine\DBAL\Connection;

final class Postgres
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Returns the auto increment value.
     *
     * Returns the value used for autoincrement tables. Usually this will just
     * be null. In case for sequence based RDBMS, this method can return a
     * proper value for the given column.
     *
     * @param string $table
     * @param string $column
     *
     * @return mixed
     */
    public function getAutoIncrementValue(string $table, string $column = 'id')
    {
        return "nextval('" . $this->connection->getDatabasePlatform()->getIdentitySequenceName($table, $column) . "')";
    }

    /**
     * Returns the last inserted ID.
     *
     * @param string $table
     * @param string $column
     *
     * @return mixed
     */
    public function lastInsertId(string $table, string $column = 'id')
    {
        return $this->connection->lastInsertId(
            $this->connection->getDatabasePlatform()->getIdentitySequenceName($table, $column)
        );
    }
}
