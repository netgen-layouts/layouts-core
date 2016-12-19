<?php

namespace Netgen\BlockManager\Layout\Container\ContainerDefinition;

use Netgen\BlockManager\Parameters\ParameterBuilderInterface;

interface DynamicContainerDefinitionHandlerInterface extends ContainerDefinitionHandlerInterface
{
    /**
     * Builds the dynamic placeholder parameters by using provided parameter builder.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     */
    public function buildDynamicPlaceholderParameters(ParameterBuilderInterface $builder);
}
