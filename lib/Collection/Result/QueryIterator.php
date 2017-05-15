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
    public function __construct(Query $query = null, $offset = 0, $limit = null)
    {
        $this->query = $query;
        $this->offset = (int) $offset;
        $this->limit = $limit !== null ? (int) $limit : null;

        parent::__construct($this->buildIterator());
    }

    /**
     * Count elements of an object.
     *
     * @return int
     */
    public function count()
    {
        if (!$this->query instanceof Query) {
            return 0;
        }

        return $this->query->getQueryType()->getCount($this->query);
    }

    /**
     * Checks if current position is valid.
     *
     * @return bool
     */
    public function valid()
    {
        if (!$this->query instanceof Query) {
            return false;
        }

        return parent::valid();
    }

    /**
     * Returns an iterator that iterates over the collection query.
     *
     * @return \Iterator
     */
    protected function buildIterator()
    {
        if ($this->limit === 0) {
            return new ArrayIterator();
        }

        if (!$this->query instanceof Query) {
            return new ArrayIterator();
        }

        $queryValues = $this->query->getQueryType()->getValues($this->query);

        $values = new AppendIterator();
        $values->append(new ArrayIterator($queryValues));

        // Make sure that we limit the number of items to actual limit if it exists
        $values = new LimitIterator(
            $values,
            $this->offset >= 0 ? $this->offset : 0,
            $this->limit > 0 ? $this->limit : -1
        );

        return $values;
    }
}
