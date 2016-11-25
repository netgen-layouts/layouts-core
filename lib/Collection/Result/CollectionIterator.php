<?php

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Countable;
use Iterator;

class CollectionIterator implements Iterator, Countable
{
    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Collection
     */
    protected $collection;

    /**
     * @var int
     */
    protected $offset;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var int
     */
    protected $pointer;

    /**
     * @var \Netgen\BlockManager\Collection\Result\CollectionQueryIterator
     */
    protected $collectionQueryIterator;

    /**
     * @var \Iterator
     */
    protected $queryIterator;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $offset
     * @param int $limit
     */
    public function __construct(Collection $collection, $offset = 0, $limit = null)
    {
        $this->collection = $collection;
        $this->offset = $offset;
        $this->limit = $limit;

        $this->pointer = $this->offset;

        $manualItemsPositions = array_keys($collection->getManualItems());

        $numberOfItemsBeforeOffset = $this->getCountBeforeOffset($manualItemsPositions, $offset);
        $numberOfItemsAtOffset = $this->getCountAtOffset($manualItemsPositions, $offset, $limit);

        $this->collectionQueryIterator = new CollectionQueryIterator(
            $this->collection,
            $offset - $numberOfItemsBeforeOffset,
            $limit !== null ? $limit - $numberOfItemsAtOffset : null
        );

        $this->queryIterator = $this->collectionQueryIterator->getIterator();
    }

    /**
     * Count the elements of the collection.
     *
     * @return int
     */
    public function count()
    {
        $totalCount = 0;

        $queryCount = $this->collectionQueryIterator->count();

        for ($i = 0; ; ++$i) {
            if ($this->collection->hasOverrideItem($i)) {
                ++$totalCount;
                --$queryCount;
            } elseif ($this->collection->hasManualItem($i)) {
                ++$totalCount;
            } elseif ($queryCount > 0) {
                ++$totalCount;
                --$queryCount;
            } else {
                break;
            }
        }

        return $totalCount;
    }

    /**
     * Return the current element.
     *
     * @return mixed
     */
    public function current()
    {
        if ($this->collection->hasOverrideItem($this->pointer)) {
            return $this->collection->getOverrideItem($this->pointer);
        } elseif ($this->collection->hasManualItem($this->pointer)) {
            return $this->collection->getManualItem($this->pointer);
        }

        return $this->queryIterator->current();
    }

    /**
     * Move forward to next element.
     */
    public function next()
    {
        if ($this->advanceQuery()) {
            $this->queryIterator->next();
        }

        ++$this->pointer;
    }

    /**
     * Return the key of the current element.
     *
     * @return mixed
     */
    public function key()
    {
        return $this->pointer;
    }

    /**
     * Checks if current position is valid.
     *
     * @return bool
     */
    public function valid()
    {
        if ($this->limit !== null && $this->pointer >= $this->offset + $this->limit) {
            return false;
        }

        if ($this->collection->hasOverrideItem($this->pointer)) {
            return true;
        }

        if ($this->collection->hasManualItem($this->pointer)) {
            return true;
        }

        return $this->queryIterator->valid();
    }

    /**
     * Rewind the Iterator to the first element.
     */
    public function rewind()
    {
        $this->pointer = $this->offset;
        $this->queryIterator->rewind();
    }

    /**
     * Returns if the query should be advanced when calling next().
     *
     * @return bool
     */
    protected function advanceQuery()
    {
        if ($this->collection->hasOverrideItem($this->pointer)) {
            return true;
        }

        if ($this->collection->hasManualItem($this->pointer)) {
            // We don't want to advance the query iterator when using
            // a manual item, since manual items are injected between
            // query values
            return false;
        }

        return true;
    }

    /**
     * Returns the count of items at positions before the original offset.
     *
     * Example: Original offset for fetching values from the queries is 10
     *          We already have 3 items injected at positions 3, 5, 9, 15 and 17
     *          Resulting count is 3
     *
     * @param array $positions
     * @param int $offset
     *
     * @return int
     */
    protected function getCountBeforeOffset(array $positions, $offset = 0)
    {
        return count(
            array_filter(
                $positions,
                function ($position) use ($offset) {
                    return $position < $offset;
                }
            )
        );
    }

    /**
     * Returns the count of items at positions between the original offset and (offset + limit - 1).
     *
     * Example: Original offset for fetching values from the queries is 10 and limit is also 10
     *          We already have 3 items injected at positions 3, 5, 9, 15 and 17
     *          Resulting count is 2
     *
     * @param array $positions
     * @param int $offset
     * @param int $limit
     *
     * @return int
     */
    protected function getCountAtOffset(array $positions, $offset = 0, $limit = null)
    {
        return count(
            array_filter(
                $positions,
                function ($position) use ($offset, $limit) {
                    if ($limit !== null) {
                        return $position >= $offset && $position < ($offset + $limit - 1);
                    }

                    return $position >= $offset;
                }
            )
        );
    }
}
