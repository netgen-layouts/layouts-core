<?php

namespace Netgen\BlockManager\Collection;

interface QueryTypeInterface
{
    /**
     * Returns the query type.
     *
     * @return string
     */
    public function getType();

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
     * @return \Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface
     */
    public function getHandler();

    /**
     * Returns the query type configuration.
     *
     * @return \Netgen\BlockManager\Collection\QueryType\Configuration\Configuration
     */
    public function getConfig();
}
