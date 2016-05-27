<?php

namespace Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolver;

use Doctrine\DBAL\Query\QueryBuilder;

interface TargetHandler
{
    /**
     * Handles the query by adding the clause that matches the provided target values.
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $query
     * @param mixed $value
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function handleQuery(QueryBuilder $query, $value);
}
