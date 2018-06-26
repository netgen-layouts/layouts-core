<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Collection\Result;

use Iterator;
use Netgen\BlockManager\API\Values\Collection\Query;

interface QueryRunnerInterface
{
    /**
     * Runs the provided query with offset and limit and returns
     * the iterator which can be used to iterate over the results.
     */
    public function runQuery(Query $query, int $offset = 0, ?int $limit = null): Iterator;

    /**
     * Returns the count of items in the provided query.
     */
    public function count(Query $query): int;
}
