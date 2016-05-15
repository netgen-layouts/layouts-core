<?php

namespace Netgen\BlockManager\Tests\Collection\Stubs\QueryRunner;

use Netgen\BlockManager\Collection\QueryType as BaseQueryType;

class QueryType extends BaseQueryType
{
    protected $queryType;

    /**
     * @var array
     */
    protected $values = array();

    /**
     * Constructor.
     *
     * @param string $queryType
     * @param array $values
     */
    public function __construct($queryType, array $values = array())
    {
        $this->queryType = $queryType;
        $this->values = $values;
    }

    /**
     * Returns the query type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->queryType;
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
        return array_slice($this->values, $offset, $limit);
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
        return count($this->values);
    }
}
