<?php

namespace Netgen\BlockManager\API\Values\Config;

use Netgen\BlockManager\API\Values\ParameterBasedValue;

interface Config extends ParameterBasedValue
{
    /**
     * @return string
     */
    public function getIdentifier();

    /**
     * @return \Netgen\BlockManager\Config\ConfigDefinitionInterface
     */
    public function getDefinition();
}
