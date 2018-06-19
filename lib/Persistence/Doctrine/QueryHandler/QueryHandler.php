<?php

declare(strict_types=1);

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

    public function __construct(Connection $connection, ConnectionHelper $connectionHelper)
    {
        $this->connection = $connection;
        $this->connectionHelper = $connectionHelper;
    }

    /**
     * Applies status condition to the query.
     */
    public function applyStatusCondition(QueryBuilder $query, ?int $status, string $statusColumn = 'status', string $paramName = 'status'): void
    {
        $query->andWhere($query->expr()->eq($statusColumn, ':' . $paramName))
            ->setParameter($paramName, $status, Type::INTEGER);
    }

    /**
     * Applies offset and limit to the query.
     */
    public function applyOffsetAndLimit(QueryBuilder $query, ?int $offset, ?int $limit): void
    {
        $query->setFirstResult($offset ?? 0);

        if (is_int($limit) && $limit > 0) {
            $query->setMaxResults($limit);
        }
    }
}
