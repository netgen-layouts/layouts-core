<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Result;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Utils\HydratorTrait;
use Traversable;

use function count;

use const PHP_INT_MAX;

/**
 * Result set is a calculated result of the collection
 * containing manual items + items received from running the query.
 *
 * @implements \IteratorAggregate<int, \Netgen\Layouts\Collection\Result\Result>
 * @implements \ArrayAccess<int, \Netgen\Layouts\Collection\Result\Result>
 */
final class ResultSet implements ArrayAccess, IteratorAggregate, Countable
{
    use HydratorTrait;

    /**
     * If specified, the result will include any invalid items,
     * i.e. those that don't exist in backend or are invisible.
     */
    public const int INCLUDE_INVALID_ITEMS = 1;

    /**
     * If provided, any items not currently known will be shown as placeholders.
     *
     * Some items may not be known for example when query is a contextual one,
     * meaning that it cannot run when there's no real frontend request,
     * e.g. in layout editing app.
     */
    public const int INCLUDE_UNKNOWN_ITEMS = 2;

    /**
     * If provided, will include all items, without any filters.
     */
    public const int INCLUDE_ALL_ITEMS = PHP_INT_MAX;

    private Collection $collection;

    /**
     * @var \Netgen\Layouts\Collection\Result\Result[]
     */
    private array $results;

    private int $totalCount;

    private int $offset;

    private ?int $limit;

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
     * @return \Netgen\Layouts\Collection\Result\Result[]
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
        return $this->collection->hasQuery;
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

        return $collectionQuery->isContextual;
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

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->results);
    }

    public function count(): int
    {
        return count($this->results);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->results[$offset]);
    }

    public function offsetGet(mixed $offset): Result
    {
        return $this->results[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): never
    {
        throw new RuntimeException('Method call not supported.');
    }

    public function offsetUnset(mixed $offset): never
    {
        throw new RuntimeException('Method call not supported.');
    }
}
