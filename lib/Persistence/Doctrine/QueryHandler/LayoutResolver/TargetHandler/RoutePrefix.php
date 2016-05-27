<?php

namespace Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolver\TargetHandler;

use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolver\TargetHandler;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Type;

class RoutePrefix implements TargetHandler
{
    /**
     * Handles the query by adding the clause that matches the provided target values.
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $query
     * @param mixed $value
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function handleQuery(QueryBuilder $query, $value)
    {
        $databasePlatform = $query->getConnection()->getDatabasePlatform();

        $query->andWhere(
            $query->expr()->andX(
                $query->expr()->gt(
                    $databasePlatform->getLengthExpression(
                        $databasePlatform->getTrimExpression('rt.value')
                    ),
                    0
                ),
                $query->expr()->like(
                    ':target_value',
                    $databasePlatform->getConcatExpression(
                        $databasePlatform->getTrimExpression('rt.value'),
                        "'%'"
                    )
                )
            )
        )
        ->setParameter(':target_value', $value, Type::STRING);

        return $query;
    }
}
