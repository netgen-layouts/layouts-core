<?php

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\API\Values\Collection\Collection;
use IteratorAggregate;
use AppendIterator;
use ArrayIterator;
use LimitIterator;
use Countable;

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
        $manualItemsPositions = array_keys($collection->getManualItems());

        $numberOfItemsBeforeOffset = $this->getCountBeforeOffset($manualItemsPositions, $offset);
        $numberOfItemsAtOffset = $this->getCountAtOffset($manualItemsPositions, $offset, $limit);

        $this->collection = $collection;
        $this->offset = $offset - $numberOfItemsBeforeOffset;
        $this->limit = $limit !== null ? $limit - $numberOfItemsAtOffset : null;
    }

    /**
     * Returns a generator that iterates over all collection queries.
     *
     * @return \Iterator
     */
    public function getIterator()
    {
        if ($this->limit !== null && $this->limit == 0) {
            return new ArrayIterator();
        }

        $values = new AppendIterator();
        $currentCount = 0;
        $realCount = 0;

        foreach ($this->collection->getQueries() as $query) {
            $queryType = $query->getQueryType();
            $queryParameters = $query->getParameters();

            $queryCount = $queryType->getCount($queryParameters);

            // We're running queries only when we reach the wanted offset
            if ($currentCount + $queryCount > $this->offset) {
                $queryValues = $queryType->getValues(
                    $queryParameters,
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
            $totalCount += $query->getQueryType()->getCount(
                $query->getParameters()
            );
        }

        return $totalCount;
    }

    /**
     * Returns the count of items at positions before the original offset.
     *
     * Example: Original offset for fetching values from the queries is 10
     *          We already have 3 items injected at positions 3, 5, 9, 15 and 17
     *          Resulting count is 3
     *
     * @param array $positions
     * @param int $offset
     *
     * @return int
     */
    protected function getCountBeforeOffset(array $positions, $offset = 0)
    {
        return count(
            array_filter(
                $positions,
                function ($position) use ($offset) {
                    return $position < $offset;
                }
            )
        );
    }

    /**
     * Returns the count of items at positions between the original offset and (offset + limit - 1).
     *
     * Example: Original offset for fetching values from the queries is 10 and limit is also 10
     *          We already have 3 items injected at positions 3, 5, 9, 15 and 17
     *          Resulting count is 2
     *
     * @param array $positions
     * @param int $offset
     * @param int $limit
     *
     * @return int
     */
    protected function getCountAtOffset(array $positions, $offset = 0, $limit = null)
    {
        return count(
            array_filter(
                $positions,
                function ($position) use ($offset, $limit) {
                    if ($limit !== null) {
                        return $position >= $offset && $position < ($offset + $limit - 1);
                    }

                    return $position >= $offset;
                }
            )
        );
    }
}
