<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Config;

use Netgen\BlockManager\Config\ConfigDefinitionInterface;
use Netgen\BlockManager\Parameters\ParameterCollectionInterface;

interface Config extends ParameterCollectionInterface
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
