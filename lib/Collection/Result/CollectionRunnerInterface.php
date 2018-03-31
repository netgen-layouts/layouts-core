<?php

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\API\Values\Collection\Collection;

interface CollectionRunnerInterface
{
    /**
     * Runs the provided collection with offset and limit and returns
     * the iterator which can be used to iterate over the results.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $offset
     * @param int $limit
     *
     * @return \Iterator
     */
    public function __invoke(Collection $collection, $offset, $limit);

    /**
     * Returns the count of items in the provided collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @return int
     */
    public function count(Collection $collection);
}
