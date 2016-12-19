<?php

namespace Netgen\BlockManager\Layout\Container\ContainerDefinition\Handler;

use Netgen\BlockManager\Layout\Container\ContainerDefinition\DynamicContainerDefinitionHandler;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;

class DynamicContainerHandler extends DynamicContainerDefinitionHandler
{
    /**
     * Builds the parameters by using provided parameter builder.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     */
    public function buildParameters(ParameterBuilderInterface $builder)
    {
        $this->buildCommonParameters($builder);
    }

    /**
     * Builds the dynamic placeholder parameters by using provided parameter builder.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     */
    public function buildDynamicPlaceholderParameters(ParameterBuilderInterface $builder)
    {
        $this->buildCommonParameters($builder);
    }

    /**
     * Returns placeholder identifiers.
     *
     * @return array
     */
    public function getPlaceholderIdentifiers()
    {
        return array();
    }
}
