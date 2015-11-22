<?php

namespace Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler;

use Doctrine\DBAL\Types\Type;
use Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler;
use Doctrine\DBAL\Query\QueryBuilder;

class RoutePrefix extends TargetHandler
{
    /**
     * Returns the target identifier this handler handles.
     *
     * @return string
     */
    public function getTargetIdentifier()
    {
        return 'route_prefix';
    }

    /**
     * Handles the query by adding the clause that matches the provided values.
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $query
     * @param array $values
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function handleQuery(QueryBuilder $query, array $values)
    {
        $databasePlatform = $query->getConnection()->getDatabasePlatform();

        $query->andWhere(
            $query->expr()->andX(
                $query->expr()->gt(
                    $databasePlatform->getLengthExpression(
                        $databasePlatform->getTrimExpression('rv.value')
                    ),
                    0
                ),
                $query->expr()->like(
                    ':rule_value',
                    $databasePlatform->getConcatExpression(
                        $databasePlatform->getTrimExpression('rv.value'),
                        "'%'"
                    )
                )
            )
        )
        ->setParameter(':rule_value', $values[0], Type::STRING);

        return $query;
    }
}
