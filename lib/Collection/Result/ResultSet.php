<?php

namespace Netgen\BlockManager\Collection\Result;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Value;

/**
 * Result set is a calculated result of the collection
 * containing manual items + items received from running the query.
 */
final class ResultSet extends Value implements ArrayAccess, IteratorAggregate, Countable
{
    /**
     * If specified, the result will include any invalid items,
     * i.e. those that don't exist in backend or are invisible.
     */
    const INCLUDE_INVALID_ITEMS = 1;

    /**
     * If provided, any items not currently known will be shown as placeholder slots.
     *
     * Slot may not be populated for example when query is a contextual one,
     * meaning that it cannot run when there's no real frontend request,
     * e.g. in Block Manager app.
     */
    const INCLUDE_UNKNOWN_ITEMS = 2;

    /**
     * If provided, will include all items, without any filters.
     */
    const INCLUDE_ALL_ITEMS = PHP_INT_MAX;

    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Collection
     */
    protected $collection;

    /**
     * @var \Netgen\BlockManager\Collection\Result\Result[]
     */
    protected $results;

    /**
     * @var int
     */
    protected $totalCount;

    /**
     * @var int
     */
    protected $offset;

    /**
     * @var int
     */
    protected $limit;

    /**
     * Returns the collection from which was this result generated.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Returns the results.
     *
     * @return \Netgen\BlockManager\Collection\Result\Result[]
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * Returns if the result set is dynamic (i.e. if generated from the dynamic collection).
     *
     * @return bool
     */
    public function isDynamic()
    {
        return $this->collection->hasQuery();
    }

    /**
     * Returns if the result set is dependent on a context, i.e. current request.
     *
     * @return bool
     */
    public function isContextual()
    {
        if (!$this->collection->hasQuery()) {
            return false;
        }

        return $this->collection->getQuery()->isContextual();
    }

    /**
     * Returns the total count of items in this result.
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * Returns the offset with which was this result generated.
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Returns the limit with which was this result generated.
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->results);
    }

    public function count()
    {
        return count($this->results);
    }

    public function offsetExists($offset)
    {
        return isset($this->results[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->results[$offset];
    }

    public function offsetSet($offset, $value)
    {
        throw new RuntimeException('Method call not supported.');
    }

    public function offsetUnset($offset)
    {
        throw new RuntimeException('Method call not supported.');
    }
}
