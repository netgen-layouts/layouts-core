<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Registry;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Netgen\Layouts\Collection\QueryType\QueryTypeInterface;

interface QueryTypeRegistryInterface extends IteratorAggregate, Countable, ArrayAccess
{
    /**
     * Returns if registry has a query type.
     */
    public function hasQueryType(string $type): bool;

    /**
     * Returns a query type with provided identifier.
     *
     * @throws \Netgen\Layouts\Exception\Collection\QueryTypeException If query type does not exist
     */
    public function getQueryType(string $type): QueryTypeInterface;

    /**
     * Returns all query types.
     *
     * @param bool $onlyEnabled
     *
     * @return \Netgen\Layouts\Collection\QueryType\QueryTypeInterface[]
     */
    public function getQueryTypes(bool $onlyEnabled = false): array;
}
