<?php

namespace Netgen\BlockManager\Core\Persistence\Doctrine\Helpers\ConnectionHelper;

use Netgen\BlockManager\Core\Persistence\Doctrine\Helpers\ConnectionHelper;
use Doctrine\DBAL\Connection;

class Sqlite extends ConnectionHelper
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
        $query = $this->connection->createQueryBuilder();
        $query->select($this->connection->getDatabasePlatform()->getMaxExpression($column) . ' AS id')
            ->from($table);

        $data = $query->execute()->fetchAll();

        return isset($data[0]['id']) ? (int)$data[0]['id'] + 1 : 1;
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
        $query = $this->connection->createQueryBuilder();
        $query->select($this->connection->getDatabasePlatform()->getMaxExpression($column) . ' AS id')
            ->from($table);

        $data = $query->execute()->fetchAll();

        return isset($data[0]['id']) ? (int)$data[0]['id'] : 0;
    }
}
