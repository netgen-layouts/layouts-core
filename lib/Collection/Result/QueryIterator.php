<?php

namespace Netgen\BlockManager\Collection\Result;

use ArrayIterator;
use Countable;
use LimitIterator;
use Netgen\BlockManager\API\Values\Collection\Query;
use OutOfBoundsException;

class QueryIterator extends LimitIterator implements Countable
{
    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Query
     */
    protected $query;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param int $offset
     * @param int $limit
     */
    public function __construct(Query $query, $offset = 0, $limit = null)
    {
        $this->query = $query;

        $offset = (int) $offset;
        $limit = $limit !== null ? (int) $limit : null;

        parent::__construct(
            $this->buildIterator(),
            $offset >= 0 ? $offset : 0,
            $limit > 0 ? $limit : -1
        );
    }

    /**
     * Count elements of an object.
     *
     * @return int
     */
    public function count()
    {
        return $this->query->getQueryType()->getCount($this->query);
    }

    /**
     * Rewind the iterator to the specified starting offset.
     */
    public function rewind()
    {
        try {
            parent::rewind();
        } catch (OutOfBoundsException $e) {
            // Do nothing
        }
    }

    /**
     * Returns an iterator that iterates over the collection query.
     *
     * @return \Iterator
     */
    protected function buildIterator()
    {
        $queryValues = $this->query->getQueryType()->getValues($this->query);

        return new ArrayIterator($queryValues);
    }
}
