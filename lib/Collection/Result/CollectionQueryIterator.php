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

        $iterator = new AppendIterator();

        foreach ($this->collection->getQueries() as $query) {
            $iterator->append(
                $query->getQueryType()->getValues(
                    $query->getParameters()
                )
            );
        }

        return new LimitIterator(
            $iterator,
            $this->offset,
            $this->limit > 0 ? $this->limit : -1
        );
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
            $queryType = $query->getQueryType();
            $parameters = $query->getParameters();

            $limit = null;
            $limitParameter = $queryType->getLimitParameter();
            $queryCount = $queryType->getCount($parameters);

            if (
                isset($parameters[$limitParameter]) &&
                is_int($parameters[$limitParameter]) &&
                $parameters[$limitParameter] >= 0
            ) {
                $limit = $parameters[$limitParameter];
            }

            if ($limit !== null && $queryCount > $limit) {
                $queryCount = $limit;
            }

            $totalCount += $queryCount;
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
