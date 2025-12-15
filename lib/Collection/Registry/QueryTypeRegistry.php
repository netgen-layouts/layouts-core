<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Registry;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Netgen\Layouts\Collection\QueryType\QueryTypeInterface;
use Netgen\Layouts\Exception\Collection\QueryTypeException;
use Netgen\Layouts\Exception\RuntimeException;
use Traversable;

use function array_filter;
use function array_key_exists;
use function count;

/**
 * @implements \ArrayAccess<string, \Netgen\Layouts\Collection\QueryType\QueryTypeInterface>
 * @implements \IteratorAggregate<string, \Netgen\Layouts\Collection\QueryType\QueryTypeInterface>
 */
final class QueryTypeRegistry implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * @param array<string, \Netgen\Layouts\Collection\QueryType\QueryTypeInterface> $queryTypes
     */
    public function __construct(
        private array $queryTypes,
    ) {
        $this->queryTypes = array_filter(
            $this->queryTypes,
            static fn (QueryTypeInterface $queryType): bool => true,
        );
    }

    /**
     * Returns if registry has a query type.
     */
    public function hasQueryType(string $type): bool
    {
        return array_key_exists($type, $this->queryTypes);
    }

    /**
     * Returns a query type with provided identifier.
     *
     * @throws \Netgen\Layouts\Exception\Collection\QueryTypeException If query type does not exist
     */
    public function getQueryType(string $type): QueryTypeInterface
    {
        if (!$this->hasQueryType($type)) {
            throw QueryTypeException::noQueryType($type);
        }

        return $this->queryTypes[$type];
    }

    /**
     * Returns all query types.
     *
     * @return array<string, \Netgen\Layouts\Collection\QueryType\QueryTypeInterface>
     */
    public function getQueryTypes(bool $onlyEnabled = false): array
    {
        if (!$onlyEnabled) {
            return $this->queryTypes;
        }

        return array_filter(
            $this->queryTypes,
            static fn (QueryTypeInterface $queryType): bool => $queryType->isEnabled,
        );
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->queryTypes);
    }

    public function count(): int
    {
        return count($this->queryTypes);
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->hasQueryType($offset);
    }

    public function offsetGet(mixed $offset): QueryTypeInterface
    {
        return $this->getQueryType($offset);
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
