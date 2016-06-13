<?php

namespace Netgen\BlockManager\Collection\Registry;

use Netgen\BlockManager\Collection\QueryTypeInterface;

interface QueryTypeRegistryInterface
{
    /**
     * Adds a query type to registry.
     *
     * @param \Netgen\BlockManager\Collection\QueryTypeInterface $queryType
     */
    public function addQueryType(QueryTypeInterface $queryType);

    /**
     * Returns if registry has a query type.
     *
     * @param string $type
     *
     * @return bool
     */
    public function hasQueryType($type);

    /**
     * Returns a query type with provided identifier.
     *
     * @param string $type
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If query type does not exist
     *
     * @return \Netgen\BlockManager\Collection\QueryTypeInterface
     */
    public function getQueryType($type);

    /**
     * Returns all query types.
     *
     * @return \Netgen\BlockManager\Collection\QueryTypeInterface[]
     */
    public function getQueryTypes();
}
