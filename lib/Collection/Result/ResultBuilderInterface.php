<?php

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\API\Values\Collection\Collection;

interface ResultBuilderInterface
{
    const INCLUDE_INVISIBLE_ITEMS = 1;

    const INCLUDE_INVALID_ITEMS = 2;

    /**
     * Builds the collection result from provided collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $offset
     * @param int $limit
     * @param int $flags
     *
     * @return \Netgen\BlockManager\Collection\Result\Result
     */
    public function buildResult(Collection $collection, $offset = 0, $limit = null, $flags = 0);
}
