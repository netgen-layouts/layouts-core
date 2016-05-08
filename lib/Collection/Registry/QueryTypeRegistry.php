<?php

namespace Netgen\BlockManager\Collection\Registry;

use Netgen\BlockManager\Collection\QueryTypeInterface;
use InvalidArgumentException;

class QueryTypeRegistry implements QueryTypeRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Collection\QueryTypeInterface[]
     */
    protected $queryTypes = array();

    /**
     * Returns a query type.
     *
     * @param string $type
     *
     * @throws \InvalidArgumentException If query type does not exist
     *
     * @return \Netgen\BlockManager\Collection\QueryTypeInterface
     */
    public function getQueryType($type)
    {
        if (!$this->hasQueryType($type)) {
            throw new InvalidArgumentException(
                'Query type "' . $type . '" does not exist.'
            );
        }

        return $this->queryTypes[$type];
    }

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
     * Returns all query types.
     *
     * @return \Netgen\BlockManager\Collection\QueryTypeInterface[]
     */
    public function getQueryTypes()
    {
        return $this->queryTypes;
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
}
