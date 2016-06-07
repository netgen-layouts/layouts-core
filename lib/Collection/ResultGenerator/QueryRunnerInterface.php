<?php

namespace Netgen\BlockManager\Collection\ResultGenerator;

interface QueryRunnerInterface
{
    /**
     * Runs all the provided queries.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query[] $queries
     * @param int $offset
     * @param int $limit
     * @param bool $includeInvisible
     *
     * @throws \RuntimeException If there are no queries to run
     *
     * @return mixed
     */
    public function runQueries(array $queries, $offset = 0, $limit = null, $includeInvisible = false);

    /**
     * Returns the total count of all queries.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query[] $queries
     * @param bool $includeInvisible
     *
     * @return int
     */
    public function getTotalCount(array $queries, $includeInvisible = false);
}
