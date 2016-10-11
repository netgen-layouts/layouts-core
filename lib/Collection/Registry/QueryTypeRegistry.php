<?php

namespace Netgen\BlockManager\Collection\Registry;

use Netgen\BlockManager\Collection\QueryTypeInterface;
use Netgen\BlockManager\Exception\InvalidArgumentException;

class QueryTypeRegistry implements QueryTypeRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Collection\QueryTypeInterface[]
     */
    protected $queryTypes = array();

    /**
     * Adds a query type to registry.
     *
     * @param \Netgen\BlockManager\Collection\QueryTypeInterface $queryType
     */
    public function addQueryType(QueryTypeInterface $queryType)
    {
        $this->queryTypes[$queryType->getType()] = $queryType;
    }

    /**
     * Returns if registry has a query type.
     *
     * @param string $type
     *
     * @return bool
     */
    public function hasQueryType($type)
    {
        return isset($this->queryTypes[$type]);
    }

    /**
     * Returns a query type with provided identifier.
     *
     * @param string $type
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If query type does not exist
     *
     * @return \Netgen\BlockManager\Collection\QueryTypeInterface
     */
    public function getQueryType($type)
    {
        if (!$this->hasQueryType($type)) {
            throw new InvalidArgumentException(
                'type',
                sprintf(
                    'Query type "%s" does not exist.',
                    $type
                )
            );
        }

        return $this->queryTypes[$type];
    }

    /**
     * Returns all query types.
     *
     * @return \Netgen\BlockManager\Collection\QueryTypeInterface[]
     */
    public function getQueryTypes()
    {
        return $this->queryTypes;
    }
}
