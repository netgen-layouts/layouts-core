<?php

namespace Netgen\BlockManager\Tests\Collection\Stubs;

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
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        return $this->handler->getParameters();
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
