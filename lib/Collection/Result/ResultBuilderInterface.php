<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\API\Values\Collection\Collection;

/**
 * Result builder is a service responsible for generating a result of a collection.
 */
interface ResultBuilderInterface
{
    /**
     * Builds the result set from the provided collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $offset
     * @param int $limit
     * @param int $flags
     *
     * @return \Netgen\BlockManager\Collection\Result\ResultSet
     */
    public function build(Collection $collection, int $offset = 0, int $limit = null, int $flags = 0): ResultSet;
}
