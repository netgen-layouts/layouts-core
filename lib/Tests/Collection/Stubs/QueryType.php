<?php

namespace Netgen\BlockManager\Tests\Collection\Stubs;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Collection\QueryType\Configuration\Configuration;
use Netgen\BlockManager\Collection\QueryTypeInterface;

class QueryType implements QueryTypeInterface
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var \Netgen\BlockManager\Tests\Collection\Stubs\QueryTypeHandler
     */
    protected $handler;

    /**
     * @var array
     */
    protected $values;

    /**
     * Constructor.
     *
     * @param string $type
     * @param array $values
     * @param int $count
     */
    public function __construct($type, array $values = array(), $count = null)
    {
        $this->type = $type;
        $this->values = $values;

        $this->handler = new QueryTypeHandler($this->values, $count);
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
        return $this->handler->getParameters();
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
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     *
     * @return int
     */
    public function getCount(Query $query)
    {
        return $this->handler->getCount($query);
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
        return new Configuration($this->type, $this->type);
    }
}
