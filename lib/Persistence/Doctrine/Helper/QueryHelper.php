<?php

namespace Netgen\BlockManager\Persistence\Doctrine\Helper;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;

class QueryHelper
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper
     */
    protected $connectionHelper;

    /**
     * Constructor.
     *
     * @param \Doctrine\DBAL\Connection $connection
     * @param \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper $connectionHelper
     */
    public function __construct(Connection $connection, ConnectionHelper $connectionHelper)
    {
        $this->connection = $connection;
        $this->connectionHelper = $connectionHelper;
    }

    /**
     * Returns the instance of Doctrine query builder.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getQuery()
    {
        return $this->connection->createQueryBuilder();
    }

    /**
     * Applies status condition to the query.
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $query
     * @param int $status
     * @param string $statusColumn
     * @param string $paramName
     */
    public function applyStatusCondition(QueryBuilder $query, $status, $statusColumn = 'status', $paramName = 'status')
    {
        $query->andWhere($query->expr()->eq($statusColumn, ':' . $paramName))
            ->setParameter($paramName, $status, Type::INTEGER);
    }
}
