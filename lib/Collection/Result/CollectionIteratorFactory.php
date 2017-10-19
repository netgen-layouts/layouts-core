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
     * Builds and returns the collection iterator for provided collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $offset
     * @param int $limit
     * @param int $flags
     *
     * @return \Netgen\BlockManager\Collection\Result\CollectionIterator
     */
    public function getCollectionIterator(Collection $collection, $offset = 0, $limit = null, $flags = 0)
    {
        $showContextualSlots = (bool) ($flags & ResultSet::INCLUDE_UNKNOWN_ITEMS);
        if ($collection->hasQuery() && $collection->getQuery()->isContextual() && $showContextualSlots) {
            $limit = $limit > 0 && $limit < $this->contextualQueryLimit ? $limit : $this->contextualQueryLimit;
        }

        $queryOffset = $offset - $this->getManualItemsCount($collection, 0, $offset);
        $queryLimit = $limit - $this->getManualItemsCount($collection, $offset, $offset + $limit);

        $queryIterator = $this->getQueryIterator($collection, $queryOffset, $queryLimit, $flags);

        return new CollectionIterator($collection, $queryIterator, $offset, $limit);
    }

    /**
     * Builds the query iterator for use by the collection iterator.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $offset
     * @param int $limit
     * @param int $flags
     *
     * @return \Iterator
     */
    private function getQueryIterator(Collection $collection, $offset = 0, $limit = null, $flags = 0)
    {
        if (!$collection->hasQuery()) {
            return new ArrayIterator();
        }

        $showContextualSlots = (bool) ($flags & ResultSet::INCLUDE_UNKNOWN_ITEMS);

        $query = $collection->getQuery();
        if ($query->isContextual() && $showContextualSlots) {
            return new ContextualQueryIterator($query, 0, $limit);
        }

        return new QueryIterator($query, $offset, $limit);
    }

    /**
     * Returns the count of manual items in a collection between $startOffset
     * and $endOffset.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $startOffset
     * @param int $endOffset
     *
     * @return int
     */
    private function getManualItemsCount(Collection $collection, $startOffset, $endOffset)
    {
        return count(
            array_filter(
                array_keys($collection->getManualItems()),
                function ($position) use ($startOffset, $endOffset) {
                    return $position >= $startOffset && $position < $endOffset;
                }
            )
        );
    }
}
