<?php

namespace Netgen\BlockManager\API\Values;

interface ParameterBasedValue
{
    /**
     * Returns all parameter values.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterValue[]
     */
    public function getParameters();

    /**
     * Returns the specified parameter value.
     *
     * @param string $parameter
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the requested parameter does not exist
     *
     * @return \Netgen\BlockManager\Parameters\ParameterValue
     */
    public function getParameter($parameter);

    /**
     * Returns if the object has a specified parameter value.
     *
     * @param string $parameter
     *
     * @return bool
     */
    public function hasParameter($parameter);
}
