<?php

namespace Netgen\BlockManager\Collection\Result;

use ArrayIterator;
use Netgen\BlockManager\API\Values\Collection\Collection;

class CollectionIteratorFactory
{
    /**
     * Builds and returns result iterator from provided collection iterator.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $flags
     *
     * @return \Netgen\BlockManager\Collection\Result\CollectionIterator
     */
    public function getCollectionIterator(Collection $collection, $flags = 0)
    {
        $queryIterator = $this->getQueryIterator($collection, $flags);

        return new CollectionIterator($collection, $queryIterator);
    }

    /**
     * Builds the query iterator for use by the collection iterator.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $flags
     *
     * @return \Iterator
     */
    protected function getQueryIterator(Collection $collection, $flags = 0)
    {
        if (!$collection->hasQuery()) {
            return new ArrayIterator();
        }

        return new QueryIterator($collection->getQuery());
    }
}
