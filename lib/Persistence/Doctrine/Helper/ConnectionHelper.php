<?php

namespace Netgen\BlockManager\Persistence\Doctrine\Helper;

use Doctrine\DBAL\Connection;
use Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper\Postgres;
use Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper\Sqlite;

class ConnectionHelper
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper[]
     */
    private $databaseSpecificHelpers = array();

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;

        $this->databaseSpecificHelpers = array(
            'sqlite' => new Sqlite($this->connection),
            'postgresql' => new Postgres($this->connection),
        );
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
    public function getAutoIncrementValue($table, $column = 'id')
    {
        $databaseServer = $this->connection->getDatabasePlatform()->getName();
        if (isset($this->databaseSpecificHelpers[$databaseServer])) {
            return $this->databaseSpecificHelpers[$databaseServer]
                ->getAutoIncrementValue($table, $column);
        }

        return 'null';
    }

    /**
     * Returns the last inserted ID.
     *
     * @param string $table
     * @param string $column
     *
     * @return mixed
     */
    public function lastInsertId($table, $column = 'id')
    {
        $databaseServer = $this->connection->getDatabasePlatform()->getName();
        if (isset($this->databaseSpecificHelpers[$databaseServer])) {
            return $this->databaseSpecificHelpers[$databaseServer]
                ->lastInsertId($table, $column);
        }

        return $this->connection->lastInsertId($table);
    }
}
