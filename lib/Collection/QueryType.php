<?php

namespace Netgen\BlockManager\Collection;

use Netgen\BlockManager\API\Values\Collection\Query;
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
     * @var \Netgen\BlockManager\Parameters\ParameterDefinitionInterface[]
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
     * @return \Netgen\BlockManager\Parameters\ParameterDefinitionInterface[]
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
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param int $offset
     * @param int $limit
     *
     * @return mixed[]
     */
    public function getValues(Query $query, $offset = 0, $limit = null)
    {
        return $this->handler->getValues($query, $offset, $limit);
    }

    /**
     * Returns the value count from the query.
     *
     * To the outside world, query count is whatever the query returns
     * based on parameter values. This may not correspond to inner query count
     * when parameters themselves contain offset and limit parameters which are then
     * used for inner query.
     *
     * Due to that, this method takes the inner query limit (as used in parameters)
     * and returns it instead if returned count is larger.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     *
     * @return int
     */
    public function getCount(Query $query)
    {
        $queryCount = $this->handler->getCount($query);

        $internalLimit = $this->handler->getInternalLimit($query);
        if ($internalLimit !== null && $queryCount > $internalLimit) {
            $queryCount = $internalLimit;
        }

        return $queryCount;
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
