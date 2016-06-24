<?php

namespace Netgen\BlockManager\Tests\Collection\Stubs;

use Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface;
use Netgen\BlockManager\Parameters\Parameter\Integer;
use Netgen\BlockManager\Parameters\Parameter\TextLine;

class QueryTypeHandler implements QueryTypeHandlerInterface
{
    /**
     * @var array
     */
    protected $values = array();

    /**
     * Constructor.
     *
     * @param array $values
     */
    public function __construct(array $values = array())
    {
        $this->values = $values;
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
        return array(
            'offset' => new Integer(),
            'param' => new TextLine(),
        );
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
