<?php

namespace Netgen\BlockManager\Collection\Result;

use AppendIterator;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use LimitIterator;
use Netgen\BlockManager\API\Values\Collection\Collection;

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
     * Returns a generator that iterates over all collection queries.
     *
     * @return \Iterator
     */
    public function getIterator()
    {
        if ($this->limit !== null && $this->limit === 0) {
            return new ArrayIterator();
        }

        $values = new AppendIterator();
        $currentCount = 0;
        $realCount = 0;

        foreach ($this->collection->getQueries() as $query) {
            $queryType = $query->getQueryType();

            $queryCount = $queryType->getCount($query);

            // We're running queries only when we reach the wanted offset
            if ($currentCount + $queryCount > $this->offset) {
                $queryValues = $queryType->getValues(
                    $query,
                    // We always use the offset of 0 for query fetches
                    // except for the first time, when we skip the number
                    // of items that were needed to finally go over the offset
                    $realCount === 0 && $this->offset > 0 ?
                        $this->offset - $currentCount :
                        0
                );

                $values->append(new ArrayIterator($queryValues));
                $realCount += count($queryValues);

                // When we have enough results make sure that we limit
                // the number of items to actual limit if it exists
                if ($this->limit > 0 && $realCount >= $this->limit) {
                    $values = new LimitIterator($values, 0, $this->limit);
                    break;
                }
            }

            $currentCount = $currentCount + $queryCount;
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
        $totalCount = 0;

        foreach ($this->collection->getQueries() as $query) {
            $totalCount += $query->getQueryType()->getCount($query);
        }

        return $totalCount;
    }
}
