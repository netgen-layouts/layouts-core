<?php

namespace Netgen\BlockManager\Config\ConfigDefinition;

use Netgen\BlockManager\Parameters\ParameterBuilderInterface;

interface ConfigDefinitionHandlerInterface
{
    /**
     * Builds the parameters by using provided parameter builder.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     */
    public function buildParameters(ParameterBuilderInterface $builder);
}
