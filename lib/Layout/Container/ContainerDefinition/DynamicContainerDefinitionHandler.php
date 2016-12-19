<?php

namespace Netgen\BlockManager\Layout\Container\ContainerDefinition;

use Netgen\BlockManager\Parameters\ParameterBuilderInterface;

abstract class DynamicContainerDefinitionHandler extends ContainerDefinitionHandler implements DynamicContainerDefinitionHandlerInterface
{
    /**
     * Builds the dynamic placeholder parameters by using provided parameter builder.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     */
    public function buildDynamicPlaceholderParameters(ParameterBuilderInterface $builder)
    {
    }
}
