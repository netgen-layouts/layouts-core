<?php

namespace Netgen\BlockManager\Core\Persistence\Doctrine\Helper\ConnectionHelper;

use Netgen\BlockManager\Core\Persistence\Doctrine\Helper\ConnectionHelper;
use Doctrine\DBAL\Connection;

class Postgres extends ConnectionHelper
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     * Constructor.
     *
     * @param \Doctrine\DBAL\Connection $connection
     */
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
    public function getAutoIncrementValue($table, $column = 'id')
    {
        return "nextval('"  . $this->connection->getDatabasePlatform()->getIdentitySequenceName($table, $column) . "')";
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
        return $this->connection->lastInsertId(
            $this->connection->getDatabasePlatform()->getIdentitySequenceName($table, $column)
        );
    }
}
