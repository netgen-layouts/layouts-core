<?php

namespace Netgen\BlockManager\Parameters\Registry;

use Netgen\BlockManager\Parameters\ParameterFilterInterface;

interface ParameterFilterRegistryInterface
{
    /**
     * Adds a parameter filter to registry.
     *
     * @param $parameterType
     * @param \Netgen\BlockManager\Parameters\ParameterFilterInterface $parameterFilter
     */
    public function addParameterFilter($parameterType, ParameterFilterInterface $parameterFilter);

    /**
     * Returns all parameter filters for provided parameter type.
     *
     * @param string $parameterType
     *
     * @return \Netgen\BlockManager\Parameters\ParameterFilterInterface[]
     */
    public function getParameterFilters($parameterType);
}
