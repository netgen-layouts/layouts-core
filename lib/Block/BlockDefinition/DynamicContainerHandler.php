<?php

namespace Netgen\BlockManager\Block\BlockDefinition;

use Netgen\BlockManager\Parameters\ParameterBuilderInterface;

abstract class DynamicContainerHandler extends ContainerDefinitionHandler
{
    /**
     * Builds the dynamic placeholder parameters by using provided parameter builder.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     */
    public function buildDynamicPlaceholderParameters(ParameterBuilderInterface $builder)
    {
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

    /**
     * Returns if this block definition is a dynamic container.
     *
     * @return bool
     */
    public function isDynamicContainer()
    {
        return true;
    }
}
