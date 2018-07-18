<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Config;

use Netgen\BlockManager\Exception\Config\ConfigDefinitionException;

trait ConfigDefinitionAwareTrait
{
    /**
     * @var \Netgen\BlockManager\Config\ConfigDefinitionInterface[]
     */
    protected $configDefinitions = [];

    /**
     * Returns the config definition with provided config key.
     *
     * @throws \Netgen\BlockManager\Exception\Config\ConfigDefinitionException if config definition does not exist
     */
    public function getConfigDefinition(string $configKey): ConfigDefinitionInterface
    {
        if (!$this->hasConfigDefinition($configKey)) {
            throw ConfigDefinitionException::noConfigDefinition($configKey);
        }

        return $this->configDefinitions[$configKey];
    }

    /**
     * Returns if the config definition with provided config key exists.
     */
    public function hasConfigDefinition(string $configKey): bool
    {
        return array_key_exists($configKey, $this->configDefinitions);
    }

    /**
     * Returns the available config definitions.
     *
     * @return \Netgen\BlockManager\Config\ConfigDefinitionInterface[]
     */
    public function getConfigDefinitions(): array
    {
        return $this->configDefinitions;
    }
}
