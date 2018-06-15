<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Config;

use Netgen\BlockManager\API\Values\ParameterBasedValue;
use Netgen\BlockManager\Config\ConfigDefinitionInterface;

interface Config extends ParameterBasedValue
{
    /**
     * Returns the config key.
     *
     * @return string
     */
    public function getConfigKey(): string;

    /**
     * Returns the config definition.
     *
     * @return \Netgen\BlockManager\Config\ConfigDefinitionInterface
     */
    public function getDefinition(): ConfigDefinitionInterface;
}
