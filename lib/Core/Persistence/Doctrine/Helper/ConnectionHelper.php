<?php

namespace Netgen\BlockManager\Core\Persistence\Doctrine\Helper;

use Netgen\BlockManager\Core\Persistence\Doctrine\Helper\ConnectionHelper\Sqlite;
use Netgen\BlockManager\Core\Persistence\Doctrine\Helper\ConnectionHelper\Postgres;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;

class ConnectionHelper
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     * @var \Netgen\BlockManager\Core\Persistence\Doctrine\Helper\ConnectionHelper[]
     */
    protected $databaseSpecificHelper = array();

    /**
     * Constructor.
     *
     * @param \Doctrine\DBAL\Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;

        $this->databaseSpecificHelper = array(
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
        if (isset($this->databaseSpecificHelper[$databaseServer])) {
            return $this->databaseSpecificHelper[$databaseServer]
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
        if (isset($this->databaseSpecificHelper[$databaseServer])) {
            return $this->databaseSpecificHelper[$databaseServer]
                ->lastInsertId($table, $column);
        }

        return $this->connection->lastInsertId($table);
    }

    /**
     * Applies status condition to the query.
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $query
     * @param int $status
     */
    public function applyStatusCondition(QueryBuilder $query, $status)
    {
        $query->andWhere($query->expr()->eq('status', ':status'))
            ->setParameter('status', $status, Type::INTEGER);
    }
}
