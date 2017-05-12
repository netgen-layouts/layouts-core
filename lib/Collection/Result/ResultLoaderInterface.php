<?php

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\API\Values\Collection\Collection;

interface ResultLoaderInterface
{
    /**
     * Loads the result set for provided collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $offset
     * @param int $limit
     * @param int $flags
     *
     * @return \Netgen\BlockManager\Collection\Result\ResultSet
     */
    public function load(Collection $collection, $offset = 0, $limit = null, $flags = 0);
}
