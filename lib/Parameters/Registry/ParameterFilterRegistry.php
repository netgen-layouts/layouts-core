<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters\Registry;

use Netgen\BlockManager\Parameters\ParameterFilterInterface;

final class ParameterFilterRegistry implements ParameterFilterRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterFilterInterface[][]
     */
    private $parameterFilters = [];

    public function addParameterFilter(string $parameterType, ParameterFilterInterface $parameterFilter): void
    {
        $this->parameterFilters[$parameterType][] = $parameterFilter;
    }

    public function getParameterFilters(string $parameterType): array
    {
        return $this->parameterFilters[$parameterType] ?? [];
    }
}
