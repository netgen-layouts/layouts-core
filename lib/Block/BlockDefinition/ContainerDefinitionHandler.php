<?php

namespace Netgen\BlockManager\Block\BlockDefinition;

use Netgen\BlockManager\Parameters\ParameterBuilderInterface;

abstract class ContainerDefinitionHandler extends BlockDefinitionHandler implements ContainerDefinitionHandlerInterface
{
    /**
     * Builds the placeholder parameters by using provided parameter builders.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface[] $builders
     */
    public function buildPlaceholderParameters(array $builders)
    {
    }

    /**
     * Builds the dynamic placeholder parameters by using provided parameter builder.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     */
    public function buildDynamicPlaceholderParameters(ParameterBuilderInterface $builder)
    {
    }

    /**
     * Returns if this block definition is a dynamic container.
     *
     * @return bool
     */
    public function isDynamicContainer()
    {
        return false;
    }
}
