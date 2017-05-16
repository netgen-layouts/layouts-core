<?php

namespace Netgen\BlockManager\Collection\Result;

use ArrayIterator;
use Countable;
use IteratorIterator;
use Netgen\BlockManager\API\Values\Collection\Query;

class QueryIterator extends IteratorIterator implements Countable
{
    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Query
     */
    protected $query;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;

        parent::__construct($this->buildIterator());
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
