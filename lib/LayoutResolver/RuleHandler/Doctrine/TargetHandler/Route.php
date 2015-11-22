<?php

namespace Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler;

use Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Type;

class Route extends TargetHandler
{
    /**
     * Returns the target handler identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'route';
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
        $query->andWhere(
            $query->expr()->eq('rv.value', ':rule_value')
        )
        ->setParameter('rule_value', $values[0], Type::STRING);
    }
}
