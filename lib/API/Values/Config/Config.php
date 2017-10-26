<?php

namespace Netgen\BlockManager\API\Values\Config;

use Netgen\BlockManager\API\Values\ParameterAwareValue;

interface Config extends ParameterAwareValue
{
    /**
     * Returns the config key.
     *
     * @return string
     */
    public function getConfigKey();

    /**
     * Returns the config definition.
     *
     * @return \Netgen\BlockManager\Config\ConfigDefinitionInterface
     */
    public function getDefinition();
}
