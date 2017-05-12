<?php

namespace Netgen\BlockManager\Collection\Result;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\ValueObject;

class ResultSet extends ValueObject implements ArrayAccess, IteratorAggregate, Countable
{
    /**
     * If specified, the result will include any invisible items
     * as provided by the backend.
     */
    const INCLUDE_INVISIBLE_ITEMS = 1;

    /**
     * If specified, the result will include any invalid items,
     * i.e. those that don't exist in backend.
     */
    const INCLUDE_INVALID_ITEMS = 2;

    /**
     * If provided, any items not currently known will be shown as placeholder slots.
     *
     * Slot may not be populated for example when query is a contextual one,
     * meaning that it cannot run when there's no real frontend request,
     * e.g. in Block Manager app.
     */
    const INCLUDE_UNKNOWN_ITEMS = 4;

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
     * Returns if the result set is dynamic.
     *
     * @return bool
     */
    public function isDynamic()
    {
        return $this->collection->getType() === Collection::TYPE_DYNAMIC;
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

        $query = $this->collection->getQuery();

        return $query->getQueryType()->isContextual($query);
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

    /**
     * Retrieve an external iterator.
     *
     * @return \Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->results);
    }

    /**
     * Count elements of an object.
     *
     * @return int
     */
    public function count()
    {
        return count($this->results);
    }

    /**
     * Whether a offset exists.
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->results[$offset]);
    }

    /**
     * Offset to retrieve.
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->results[$offset];
    }

    /**
     * Offset to set.
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        throw new RuntimeException('Method call not supported.');
    }

    /**
     * Offset to unset.
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        throw new RuntimeException('Method call not supported.');
    }
}
