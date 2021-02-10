<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Config;

use Netgen\Layouts\Config\ConfigDefinitionInterface;
use Netgen\Layouts\Parameters\ParameterCollectionInterface;
use Netgen\Layouts\Parameters\ParameterCollectionTrait;
use Netgen\Layouts\Utils\HydratorTrait;

final class Config implements ParameterCollectionInterface
{
    use HydratorTrait;
    use ParameterCollectionTrait;

    private string $configKey;

    private ConfigDefinitionInterface $definition;

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
