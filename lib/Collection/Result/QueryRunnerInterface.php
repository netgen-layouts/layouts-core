<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\API\Values\Collection\Query;

interface QueryRunnerInterface
{
    /**
     * Runs the provided query with offset and limit and returns
     * the iterator which can be used to iterate over the results.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param int $offset
     * @param int $limit
     *
     * @return \Iterator
     */
    public function runQuery(Query $query, $offset = 0, $limit = null);

    /**
     * Returns the count of items in the provided query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     *
     * @return int
     */
    public function count(Query $query);
}
