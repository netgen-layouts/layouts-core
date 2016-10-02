<?php

namespace Netgen\BlockManager\Collection\QueryType;

interface QueryTypeHandlerInterface
{
    /**
     * Returns the array specifying query parameters.
     *
     * The keys are parameter identifiers.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters();

    /**
     * Returns the values from the query.
     *
     * @param array $parameters
     * @param int $offset
     * @param int $limit
     *
     * @return mixed[]
     */
    public function getValues(array $parameters, $offset = 0, $limit = null);

    /**
     * Returns the value count from the query.
     *
     * @param array $parameters
     *
     * @return int
     */
    public function getCount(array $parameters);

    /**
     * Returns the limit internal to this query.
     *
     * @param array $parameters
     *
     * @return int
     */
    public function getInternalLimit(array $parameters);
}
