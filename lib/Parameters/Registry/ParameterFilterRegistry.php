<?php

namespace Netgen\BlockManager\Parameters\Registry;

use Netgen\BlockManager\Parameters\ParameterFilterInterface;
use RuntimeException;

class ParameterFilterRegistry implements ParameterFilterRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterFilterInterface[]
     */
    protected $parameterFilters = array();

    /**
     * Adds a parameter filter to registry.
     *
     * @param $parameterType
     * @param \Netgen\BlockManager\Parameters\ParameterFilterInterface[] $parameterFilters
     */
    public function addParameterFilters($parameterType, array $parameterFilters)
    {
        foreach ($parameterFilters as $parameterFilter) {
            if (!$parameterFilter instanceof ParameterFilterInterface) {
                throw new RuntimeException(
                    sprintf(
                        'Parameter filter "%s" needs to implement ParameterFilterInterface',
                        get_class($parameterFilter)
                    )
                );
            }
        }

        $this->parameterFilters[$parameterType] = $parameterFilters;
    }

    /**
     * Returns all parameter filters for provided parameter type.
     *
     * @param string $parameterType
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If parameter filter does not exist
     *
     * @return \Netgen\BlockManager\Parameters\ParameterFilterInterface[]
     */
    public function getParameterFilters($parameterType)
    {
        return isset($this->parameterFilters[$parameterType]) ?
            $this->parameterFilters[$parameterType] :
            array();
    }
}
