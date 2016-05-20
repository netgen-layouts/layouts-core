<?php

namespace Netgen\BlockManager\Collection;

use Netgen\BlockManager\Collection\QueryType\Configuration\Configuration;
use Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface;

class QueryType implements QueryTypeInterface
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var \Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface
     */
    protected $queryTypeHandler;

    /**
     * @var \Netgen\BlockManager\Collection\QueryType\Configuration\Configuration
     */
    protected $config;

    /**
     * Constructor.
     *
     * @param string $type
     * @param \Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface $queryTypeHandler
     * @param \Netgen\BlockManager\Collection\QueryType\Configuration\Configuration $config
     */
    public function __construct($type, QueryTypeHandlerInterface $queryTypeHandler, Configuration $config)
    {
        $this->type = $type;
        $this->queryTypeHandler = $queryTypeHandler;
        $this->config = $config;
    }

    /**
     * Returns the query type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the array specifying query parameters.
     *
     * The keys are parameter types.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        return $this->queryTypeHandler->getParameters();
    }

    /**
     * Returns the values from the query.
     *
     * @param array $parameters
     * @param int $offset
     * @param int $limit
     *
     * @return mixed[]
     */
    public function getValues(array $parameters, $offset = 0, $limit = null)
    {
        return $this->queryTypeHandler->getValues($parameters, $offset, $limit);
    }

    /**
     * Returns the value count from the query.
     *
     * @param array $parameters
     *
     * @return int
     */
    public function getCount(array $parameters)
    {
        return $this->queryTypeHandler->getCount($parameters);
    }

    /**
     * Returns the query type configuration.
     *
     * @return \Netgen\BlockManager\Collection\QueryType\Configuration\Configuration
     */
    public function getConfig()
    {
        return $this->config;
    }
}
