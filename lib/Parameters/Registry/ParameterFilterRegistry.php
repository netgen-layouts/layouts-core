<?php

namespace Netgen\BlockManager\Parameters\Registry;

use Netgen\BlockManager\Parameters\ParameterFilterInterface;

class ParameterFilterRegistry implements ParameterFilterRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterFilterInterface[][]
     */
    private $parameterFilters = array();

    public function addParameterFilter($parameterType, ParameterFilterInterface $parameterFilter)
    {
        $this->parameterFilters[$parameterType][] = $parameterFilter;
    }

    public function getParameterFilters($parameterType)
    {
        return isset($this->parameterFilters[$parameterType]) ?
            $this->parameterFilters[$parameterType] :
            array();
    }
}
