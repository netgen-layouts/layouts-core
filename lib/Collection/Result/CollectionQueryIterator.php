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
        if ($this->limit !== null && $this->limit === 0) {
            return new ArrayIterator();
        }

        $values = new AppendIterator();

        $query = $this->collection->getQuery();

        if (!$query instanceof Query) {
            return $values;
        }

        $queryCount = $query->getQueryType()->getCount($query);

        if ($queryCount > 0) {
            $queryValues = $query->getQueryType()->getValues(
                $query,
                $this->offset
            );

            $values->append(new ArrayIterator($queryValues));
        }

        // Make sure that we limit the number of items to actual limit if it exists
        if ($this->limit > 0 && $queryCount >= $this->limit) {
            $values = new LimitIterator($values, 0, $this->limit);
        }

        return $values;
    }

    /**
     * Count elements of an object.
     *
     * @return int
     */
    public function count()
    {
        $query = $this->collection->getQuery();

        if (!$query instanceof Query) {
            return 0;
        }

        return $query->getQueryType()->getCount($query);
    }
}
