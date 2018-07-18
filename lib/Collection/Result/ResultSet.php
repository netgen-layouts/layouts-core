<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Collection\Result;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Utils\HydratorTrait;

/**
 * Result set is a calculated result of the collection
 * containing manual items + items received from running the query.
 */
final class ResultSet implements ArrayAccess, IteratorAggregate, Countable
{
    use HydratorTrait;

    /**
     * If specified, the result will include any invalid items,
     * i.e. those that don't exist in backend or are invisible.
     */
    public const INCLUDE_INVALID_ITEMS = 1;

    /**
     * If provided, any items not currently known will be shown as placeholder slots.
     *
     * Slot may not be populated for example when query is a contextual one,
     * meaning that it cannot run when there's no real frontend request,
     * e.g. in layout editing app.
     */
    public const INCLUDE_UNKNOWN_ITEMS = 2;

    /**
     * If provided, will include all items, without any filters.
     */
    public const INCLUDE_ALL_ITEMS = PHP_INT_MAX;

    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Collection
     */
    private $collection;

    /**
     * @var \Netgen\BlockManager\Collection\Result\Result[]
     */
    private $results;

    /**
     * @var int
     */
    private $totalCount;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var int
     */
    private $limit;

    /**
     * Returns the collection from which was this result generated.
     */
    public function getCollection(): Collection
    {
        return $this->collection;
    }

    /**
     * Returns the results.
     *
     * @return \Netgen\BlockManager\Collection\Result\Result[]
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * Returns if the result set is dynamic (i.e. if generated from the dynamic collection).
     */
    public function isDynamic(): bool
    {
        return $this->collection->hasQuery();
    }

    /**
     * Returns if the result set is dependent on a context, i.e. current request.
     */
    public function isContextual(): bool
    {
        $collectionQuery = $this->collection->getQuery();
        if (!$collectionQuery instanceof Query) {
            return false;
        }

        return $collectionQuery->isContextual();
    }

    /**
     * Returns the total count of items in this result.
     */
    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    /**
     * Returns the offset with which was this result generated.
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * Returns the limit with which was this result generated.
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->results);
    }

    public function count()
    {
        return count($this->results);
    }

    public function offsetExists($offset)
    {
        return isset($this->results[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->results[$offset];
    }

    public function offsetSet($offset, $value)
    {
        throw new RuntimeException('Method call not supported.');
    }

    public function offsetUnset($offset)
    {
        throw new RuntimeException('Method call not supported.');
    }
}
