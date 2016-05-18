<?php

namespace Netgen\BlockManager\Layout\Resolver\RuleHandler\Doctrine;

use Doctrine\DBAL\Query\QueryBuilder;

abstract class TargetHandler
{
    /**
     * Handles the query by adding the clause that matches the provided values.
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $query
     * @param array $values
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    abstract public function handleQuery(QueryBuilder $query, array $values);
}
