<?php

namespace Netgen\BlockManager\Collection\Result;

use Countable;
use Iterator;
use Netgen\BlockManager\API\Values\Collection\Collection;

final class CollectionIterator implements Iterator, Countable
{
    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Collection
     */
    private $collection;

    /**
     * @var int
     */
    private $pointer;

    /**
     * @var \Iterator
     */
    private $queryIterator;

    public function __construct(Collection $collection, Iterator $queryIterator)
    {
        $this->collection = $collection;
        $this->queryIterator = $queryIterator;

        $this->pointer = 0;
    }

    public function count()
    {
        $totalCount = 0;

        $queryCount = count($this->queryIterator);

        for ($i = 0; ; ++$i) {
            if ($this->collection->hasOverrideItem($i)) {
                ++$totalCount;
                --$queryCount;
                continue;
            }

            if ($this->collection->hasManualItem($i)) {
                ++$totalCount;
                continue;
            }

            if ($queryCount > 0) {
                ++$totalCount;
                --$queryCount;
                continue;
            }

            break;
        }

        return $totalCount;
    }

    public function current()
    {
        if ($this->collection->hasOverrideItem($this->pointer)) {
            return $this->collection->getOverrideItem($this->pointer);
        } elseif ($this->collection->hasManualItem($this->pointer)) {
            return $this->collection->getManualItem($this->pointer);
        }

        return $this->queryIterator->current();
    }

    public function next()
    {
        if ($this->advanceQuery()) {
            $this->queryIterator->next();
        }

        ++$this->pointer;
    }

    public function key()
    {
        return $this->pointer;
    }

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
    private function advanceQuery()
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
