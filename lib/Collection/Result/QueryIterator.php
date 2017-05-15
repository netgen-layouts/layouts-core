<?php

namespace Netgen\BlockManager\Collection\Result;

use AppendIterator;
use ArrayIterator;
use Countable;
use IteratorIterator;
use LimitIterator;
use Netgen\BlockManager\API\Values\Collection\Query;

class QueryIterator extends IteratorIterator implements Countable
{
    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Query
     */
    protected $query;

    /**
     * @var int
     */
    protected $offset;

    /**
     * @var int
     */
    protected $limit;

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

        if ($limit === 0) {
            parent::__construct(new ArrayIterator());

            return;
        }

        // Make sure that we limit the number of items to actual limit if it exists
        $iterator = new LimitIterator(
            $this->buildIterator(),
            $offset >= 0 ? $offset : 0,
            $limit > 0 ? $limit : -1
        );

        parent::__construct($iterator);
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

        $values = new AppendIterator();
        $values->append(new ArrayIterator($queryValues));

        return $values;
    }
}
