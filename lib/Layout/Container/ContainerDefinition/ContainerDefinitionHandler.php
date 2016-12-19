<?php

namespace Netgen\BlockManager\Layout\Container\ContainerDefinition;

use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;

abstract class ContainerDefinitionHandler implements ContainerDefinitionHandlerInterface
{
    /**
     * Builds the parameters by using provided parameter builder.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     */
    public function buildParameters(ParameterBuilderInterface $builder)
    {
    }

    /**
     * Builds the placeholder parameters by using provided parameter builders.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface[] $builders
     */
    public function buildPlaceholderParameters(array $builders)
    {
    }

    /**
     * Builds the parameters most containers will use by using provided parameter builder.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     */
    protected function buildCommonParameters(ParameterBuilderInterface $builder)
    {
        $builder->add('css_class', ParameterType\TextLineType::class);
        $builder->add('css_id', ParameterType\TextLineType::class);
    }
}
