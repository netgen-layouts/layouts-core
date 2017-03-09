<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler\Container;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;

abstract class ContainerHandler extends BlockDefinitionHandler
{
    /**
     * Builds the placeholder parameters by using provided parameter builders.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface[] $builders
     */
    public function buildPlaceholderParameters(array $builders)
    {
        foreach ($builders as $builder) {
            $this->buildCommonParameters($builder);
        }
    }

    /**
     * Returns if this block definition is a container.
     *
     * @return bool
     */
    public function isContainer()
    {
        return true;
    }
}
