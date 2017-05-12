<?php

namespace Netgen\BlockManager\Collection\Result;

use ArrayIterator;
use Netgen\BlockManager\API\Values\Collection\Collection;

class CollectionIteratorFactory
{
    /**
     * @var int
     */
    protected $contextualQueryLimit;

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
    protected function getQueryIterator(Collection $collection, $flags = 0)
    {
        if (!$collection->hasQuery()) {
            return new ArrayIterator();
        }

        $showContextualSlots = (bool) ($flags & ResultSet::INCLUDE_UNKNOWN_ITEMS);

        $query = $collection->getQuery();
        if ($query->getQueryType()->isContextual($query) && $showContextualSlots) {
            return new ContextualQueryIterator($collection->getQuery(), $this->contextualQueryLimit);
        }

        return new QueryIterator($collection->getQuery());
    }
}
