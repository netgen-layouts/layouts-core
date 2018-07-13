<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Config;

use Netgen\BlockManager\API\Values\ParameterCollection;
use Netgen\BlockManager\Config\ConfigDefinitionInterface;

interface Config extends ParameterCollection
{
    /**
     * Returns the config key.
     */
    public function getConfigKey(): string;

    /**
     * Returns the config definition.
     */
    public function getDefinition(): ConfigDefinitionInterface;
}
