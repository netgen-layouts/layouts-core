<?php

namespace Netgen\BlockManager\Collection\Result;

use ArrayIterator;
use Netgen\BlockManager\API\Values\Collection\Collection;

final class CollectionIteratorFactory
{
    /**
     * @var int
     */
    private $contextualQueryLimit;

    /**
     * Constructor.
     *
     * @param int $contextualQueryLimit
     */
    public function __construct($contextualQueryLimit)
    {
        $this->contextualQueryLimit = $contextualQueryLimit;
    }

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
    private function getQueryIterator(Collection $collection, $flags = 0)
    {
        if (!$collection->hasQuery()) {
            return new ArrayIterator();
        }

        $showContextualSlots = (bool) ($flags & ResultSet::INCLUDE_UNKNOWN_ITEMS);

        $query = $collection->getQuery();
        if ($query->isContextual() && $showContextualSlots) {
            return new ContextualQueryIterator($query, $this->contextualQueryLimit);
        }

        return new QueryIterator($query);
    }
}
