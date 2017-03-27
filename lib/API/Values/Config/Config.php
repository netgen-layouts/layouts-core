<?php

namespace Netgen\BlockManager\API\Values\Config;

use Netgen\BlockManager\API\Values\ParameterBasedValue;

interface Config extends ParameterBasedValue
{
    /**
     * Returns the config identifier.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Returns the config definition.
     *
     * @return \Netgen\BlockManager\Config\ConfigDefinitionInterface
     */
    public function getDefinition();
}
