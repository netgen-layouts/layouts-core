<?php

namespace Netgen\BlockManager\Config;

use Netgen\BlockManager\Parameters\ParameterBuilderInterface;

/**
 * Config definition handler represents the dynamic/runtime part of the
 * config definitions.
 *
 * Implement this interface to create your own config definitions for an entity.
 */
interface ConfigDefinitionHandlerInterface
{
    /**
     * Builds the parameters by using provided parameter builder.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     */
    public function buildParameters(ParameterBuilderInterface $builder);
}
