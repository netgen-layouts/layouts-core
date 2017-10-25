<?php

namespace Netgen\BlockManager\Collection\Result;

use ArrayIterator;
use Countable;
use IteratorIterator;
use Netgen\BlockManager\API\Values\Collection\Query;

final class QueryIterator extends IteratorIterator implements Countable
{
    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Query
     */
    private $query;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var int
     */
    private $limit;

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
        $this->offset = $offset;
        $this->limit = $limit;

        parent::__construct($this->buildIterator());
    }

    public function count()
    {
        return $this->query->getQueryType()->getCount($this->query);
    }

    /**
     * Returns an iterator that iterates over the collection query.
     *
     * @return \Iterator
     */
    private function buildIterator()
    {
        $queryValues = $this->query->getQueryType()->getValues(
            $this->query,
            $this->offset,
            $this->limit
        );

        return new ArrayIterator($queryValues);
    }
}
