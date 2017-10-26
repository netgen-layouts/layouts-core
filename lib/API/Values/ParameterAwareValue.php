<?php

namespace Netgen\BlockManager\API\Values;

interface ParameterAwareValue
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
     * @param string $parameterName
     *
     * @throws \Netgen\BlockManager\Exception\Core\ParameterException If the requested parameter value does not exist
     *
     * @return \Netgen\BlockManager\Parameters\ParameterValue
     */
    public function getParameter($parameterName);

    /**
     * Returns if the object has a specified parameter value.
     *
     * @param string $parameterName
     *
     * @return bool
     */
    public function hasParameter($parameterName);
}
