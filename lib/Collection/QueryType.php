<?php

namespace Netgen\BlockManager\Collection;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\ValueObject;

class QueryType extends ValueObject implements QueryTypeInterface
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
     * @var \Netgen\BlockManager\Parameters\ParameterBuilderInterface
     */
    protected $parameterBuilder;

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
            $this->handler->buildParameters($this->parameterBuilder);
            $this->parameters = $this->parameterBuilder->buildParameters();
            unset($this->parameterBuilder);
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
     * Returns the query type configuration.
     *
     * @return \Netgen\BlockManager\Collection\QueryType\Configuration\Configuration
     */
    public function getConfig()
    {
        return $this->config;
    }
}
