<?php

namespace Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine;

use Doctrine\DBAL\Query\QueryBuilder;

abstract class TargetHandler
{
    /**
     * Returns the target identifier this handler handles.
     *
     * @return string
     */
    abstract public function getTargetIdentifier();

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
