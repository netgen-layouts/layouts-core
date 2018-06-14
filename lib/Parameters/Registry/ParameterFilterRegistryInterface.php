<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters\Registry;

use Netgen\BlockManager\Parameters\ParameterFilterInterface;

interface ParameterFilterRegistryInterface
{
    /**
     * Adds a parameter filter to registry.
     *
     * @param string $parameterType
     * @param \Netgen\BlockManager\Parameters\ParameterFilterInterface $parameterFilter
     */
    public function addParameterFilter(string $parameterType, ParameterFilterInterface $parameterFilter): void;

    /**
     * Returns all parameter filters for provided parameter type.
     *
     * @param string $parameterType
     *
     * @return \Netgen\BlockManager\Parameters\ParameterFilterInterface[]
     */
    public function getParameterFilters(string $parameterType): array;
}
