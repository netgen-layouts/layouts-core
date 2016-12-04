<?php

namespace Netgen\BlockManager\Persistence\Doctrine\QueryHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Type;
use Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper;

abstract class QueryHandler
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

    /**
     * Applies offset and limit to the query.
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $query
     * @param int $offset
     * @param int $limit
     */
    public function applyOffsetAndLimit(QueryBuilder $query, $offset, $limit)
    {
        $offset = is_int($offset) ? $offset : 0;
        $limit = is_int($limit) && $limit > 0 ? $limit : null;

        $query
            ->setFirstResult($offset)
            ->setMaxResults($limit);
    }
}
