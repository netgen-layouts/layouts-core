<?php

namespace Netgen\BlockManager\Layout\Container\ContainerDefinition;

use Netgen\BlockManager\Parameters\ParameterBuilderInterface;

interface ContainerDefinitionHandlerInterface
{
    /**
     * Builds the parameters by using provided parameter builder.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     */
    public function buildParameters(ParameterBuilderInterface $builder);

    /**
     * Builds the placeholder parameters by using provided parameter builders.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface[] $builders
     */
    public function buildPlaceholderParameters(array $builders);

    /**
     * Returns placeholder identifiers.
     *
     * @return array
     */
    public function getPlaceholderIdentifiers();
}
