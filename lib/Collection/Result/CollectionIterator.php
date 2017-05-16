<?php

namespace Netgen\BlockManager\Collection\Result;

use Countable;
use Iterator;
use Netgen\BlockManager\API\Values\Collection\Collection;

class CollectionIterator implements Iterator, Countable
{
    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Collection
     */
    protected $collection;

    /**
     * @var int
     */
    protected $pointer;

    /**
     * @var \Iterator
     */
    protected $queryIterator;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param \Iterator $queryIterator
     */
    public function __construct(Collection $collection, Iterator $queryIterator)
    {
        $this->collection = $collection;
        $this->queryIterator = $queryIterator;

        $this->pointer = 0;
    }

    /**
     * Count the elements of the collection.
     *
     * @return int
     */
    public function count()
    {
        $totalCount = 0;

        $queryCount = count($this->queryIterator);

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
        $this->pointer = 0;

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
}
