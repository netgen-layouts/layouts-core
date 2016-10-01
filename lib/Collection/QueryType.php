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
    protected $handler;

    /**
     * @var \Netgen\BlockManager\Collection\QueryType\Configuration\Configuration
     */
    protected $config;

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    protected $parameters;

    /**
     * Constructor.
     *
     * @param string $type
     * @param \Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface $handler
     * @param \Netgen\BlockManager\Collection\QueryType\Configuration\Configuration $config
     */
    public function __construct($type, QueryTypeHandlerInterface $handler, Configuration $config)
    {
        $this->type = $type;
        $this->handler = $handler;
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
     * The keys are parameter identifiers.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        if ($this->parameters === null) {
            $this->parameters = $this->handler->getParameters();
        }

        return $this->parameters;
    }

    /**
     * Returns the values from the query.
     *
     * @param array $parameters
     * @param int $offset
     * @param int $limit
     *
     * @return \Iterator
     */
    public function getValues(array $parameters, $offset = 0, $limit = null)
    {
        return $this->handler->getValues($parameters, $offset, $limit);
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
        return $this->handler->getCount($parameters);
    }

    /**
     * Returns the name of the parameter which will be used as a limit inside the query.
     *
     * @return string
     */
    public function getLimitParameter()
    {
        return $this->handler->getLimitParameter();
    }

    /**
     * @return \Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface
     */
    public function getHandler()
    {
        return $this->handler;
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
