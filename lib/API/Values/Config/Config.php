<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Config;

use Netgen\BlockManager\Config\ConfigDefinitionInterface;
use Netgen\BlockManager\Parameters\ParameterCollectionInterface;
use Netgen\BlockManager\Parameters\ParameterCollectionTrait;
use Netgen\BlockManager\Utils\HydratorTrait;

final class Config implements ParameterCollectionInterface
{
    use HydratorTrait;
    use ParameterCollectionTrait;

    /**
     * @var string
     */
    private $configKey;

    /**
     * @var \Netgen\BlockManager\Config\ConfigDefinitionInterface
     */
    private $definition;

    /**
     * Returns the config key.
     */
    public function getConfigKey(): string
    {
        return $this->configKey;
    }

    /**
     * Returns the config definition.
     */
    public function getDefinition(): ConfigDefinitionInterface
    {
        return $this->definition;
    }
}
