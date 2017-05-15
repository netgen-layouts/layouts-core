<?php

namespace Netgen\BlockManager\Collection\Result;

use AppendIterator;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use LimitIterator;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\Query;

class CollectionQueryIterator implements IteratorAggregate, Countable
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
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $offset
     * @param int $limit
     */
    public function __construct(Collection $collection, $offset = 0, $limit = null)
    {
        $this->collection = $collection;
        $this->offset = (int) $offset;
        $this->limit = $limit !== null ? (int) $limit : null;
    }

    /**
     * Returns a generator that iterates over the collection query.
     *
     * @return \Iterator
     */
    public function getIterator()
    {
        if ($this->limit === 0) {
            return new ArrayIterator();
        }

        if (!$this->collection->hasQuery()) {
            return new ArrayIterator();
        }

        $query = $this->collection->getQuery();

        $values = new AppendIterator();

        $queryValues = $query->getQueryType()->getValues($query);
        $values->append(new ArrayIterator($queryValues));

        // Make sure that we limit the number of items to actual limit if it exists
        $values = new LimitIterator(
            $values,
            $this->offset >= 0 ? $this->offset : 0,
            $this->limit > 0 ? $this->limit : -1
        );

        return $values;
    }

    /**
     * Count elements of an object.
     *
     * @return int
     */
    public function count()
    {
        if (!$this->collection->hasQuery()) {
            return 0;
        }

        $query = $this->collection->getQuery();

        return $query->getQueryType()->getCount($query);
    }
}
