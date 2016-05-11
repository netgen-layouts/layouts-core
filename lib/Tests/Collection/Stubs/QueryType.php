<?php

namespace Netgen\BlockManager\Tests\Collection\Stubs;

use Netgen\BlockManager\Collection\QueryTypeInterface;
use Netgen\BlockManager\Parameters\Parameter\Text;

class QueryType implements QueryTypeInterface
{
    /**
     * Returns the query type.
     *
     * @return string
     */
    public function getType()
    {
        return 'query_type';
    }

    /**
     * Returns the array specifying query parameters.
     *
     * The keys are parameter identifiers.
     *
     * @return \Netgen\BlockManager\Parameters\Parameter[]
     */
    public function getParameters()
    {
        return array(
            'param' => new Text(),
        );
    }

    /**
     * Returns the array specifying query parameter validator constraints.
     *
     * @return array
     */
    public function getParameterConstraints()
    {
        return array();
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
    }
}
