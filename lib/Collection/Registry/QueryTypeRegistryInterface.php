<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Collection\Registry;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Netgen\BlockManager\Collection\QueryType\QueryTypeInterface;

interface QueryTypeRegistryInterface extends IteratorAggregate, Countable, ArrayAccess
{
    /**
     * Adds a query type to registry.
     */
    public function addQueryType(string $type, QueryTypeInterface $queryType): void;

    /**
     * Returns if registry has a query type.
     */
    public function hasQueryType(string $type): bool;

    /**
     * Returns a query type with provided identifier.
     *
     * @throws \Netgen\BlockManager\Exception\Collection\QueryTypeException If query type does not exist
     */
    public function getQueryType(string $type): QueryTypeInterface;

    /**
     * Returns all query types.
     *
     * @param bool $onlyEnabled
     *
     * @return \Netgen\BlockManager\Collection\QueryType\QueryTypeInterface[]
     */
    public function getQueryTypes(bool $onlyEnabled = false): array;
}
