<?php

namespace Netgen\BlockManager\Tests\Config\Stubs\Block;

use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinitionHandler;

class DisabledConfigHandler extends ConfigDefinitionHandler
{
    /**
     * Returns the array specifying block parameters.
     *
     * The keys are parameter identifiers.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        return array();
    }

    /**
     * Returns if this config definition is enabled for current config aware value.
     *
     * @param \Netgen\BlockManager\API\Values\Config\ConfigAwareValue $configAwareValue
     *
     * @return bool
     */
    public function isEnabled(ConfigAwareValue $configAwareValue)
    {
        return false;
    }
}
