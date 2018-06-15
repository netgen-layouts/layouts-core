<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters\Registry;

use Netgen\BlockManager\Parameters\ParameterFilterInterface;

interface ParameterFilterRegistryInterface
{
    /**
     * Adds a parameter filter to registry.
     */
    public function addParameterFilter(string $parameterType, ParameterFilterInterface $parameterFilter): void;

    /**
     * Returns all parameter filters for provided parameter type.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterFilterInterface[]
     */
    public function getParameterFilters(string $parameterType): array;
}
