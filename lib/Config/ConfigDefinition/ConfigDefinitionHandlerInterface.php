<?php

namespace Netgen\BlockManager\Config\ConfigDefinition;

use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;

interface ConfigDefinitionHandlerInterface
{
    /**
     * Builds the parameters by using provided parameter builder.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     */
    public function buildParameters(ParameterBuilderInterface $builder);

    /**
     * Returns if this config definition is enabled for current config aware value.
     *
     * @param \Netgen\BlockManager\API\Values\Config\ConfigAwareValue $configAwareValue
     *
     * @return bool
     */
    public function isEnabled(ConfigAwareValue $configAwareValue);
}
