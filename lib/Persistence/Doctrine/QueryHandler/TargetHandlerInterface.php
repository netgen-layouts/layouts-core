<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Doctrine\QueryHandler;

use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Classes implementing this interface process the query to add a section of the query
 * which matches the targets from the table with the provided value.
 *
 * This is done in layout resolving process to return the targets only matching
 * the specified value (extracted from the request on which the layout resolving is ran).
 */
interface TargetHandlerInterface
{
    /**
     * Handles the query by adding the clause that matches the provided target values.
     *
     * @param mixed $value
     */
    public function handleQuery(QueryBuilder $query, $value): void;
}
