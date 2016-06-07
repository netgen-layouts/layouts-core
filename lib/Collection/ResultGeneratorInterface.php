<?php

namespace Netgen\BlockManager\Collection;

use Netgen\BlockManager\API\Values\Collection\Collection;

interface ResultGeneratorInterface
{
    const INCLUDE_INVISIBLE_ITEMS = 1;

    const IGNORE_EXCEPTIONS = 2;

    /**
     * Generates the collection result from provided collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $offset
     * @param int $limit
     * @param int $flags
     *
     * @return \Netgen\BlockManager\Collection\Result
     */
    public function generateResult(Collection $collection, $offset = 0, $limit = null, $flags = 0);
}
