<?php

namespace Netgen\BlockManager\Layout\Container\ContainerDefinition\Handler;

use Netgen\BlockManager\Layout\Container\ContainerDefinition\ContainerDefinitionHandler;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;

class TwoColumnsContainerHandler extends ContainerDefinitionHandler
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
     * Returns placeholder identifiers.
     *
     * @return array
     */
    public function getPlaceholderIdentifiers()
    {
        return array('left', 'right');
    }
}
