<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Doctrine\QueryHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Types;
use Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelperInterface;

use function is_int;
use function is_string;

abstract class QueryHandler
{
    protected Connection $connection;

    protected ConnectionHelperInterface $connectionHelper;

    public function __construct(Connection $connection, ConnectionHelperInterface $connectionHelper)
    {
        $this->connection = $connection;
        $this->connectionHelper = $connectionHelper;
    }

    /**
     * Applies ID condition to the query.
     *
     * @param int|string $id
     */
    public function applyIdCondition(QueryBuilder $query, $id, string $idColumn = 'id', string $uuidColumn = 'uuid', string $idParamName = 'id', string $uuidParamName = 'uuid'): void
    {
        $isUuid = is_string($id);

        $query->andWhere(
            $isUuid ?
                $query->expr()->eq($uuidColumn, ':' . $uuidParamName) :
                $query->expr()->eq($idColumn, ':' . $idParamName),
        )->setParameter(
            $isUuid ? $uuidParamName : $idParamName,
            $id,
            $isUuid ? Types::STRING : Types::INTEGER,
        );
    }

    /**
     * Applies status condition to the query.
     */
    public function applyStatusCondition(QueryBuilder $query, ?int $status, string $statusColumn = 'status', string $paramName = 'status'): void
    {
        $query->andWhere($query->expr()->eq($statusColumn, ':' . $paramName))
            ->setParameter($paramName, $status, Types::INTEGER);
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
