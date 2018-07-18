<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Config;

interface ConfigDefinitionAwareInterface
{
    /**
     * Returns the config definition with provided config key.
     *
     * @throws \Netgen\BlockManager\Exception\Config\ConfigDefinitionException if config definition does not exist
     */
    public function getConfigDefinition(string $configKey): ConfigDefinitionInterface;

    /**
     * Returns if the config definition with provided config key exists.
     */
    public function hasConfigDefinition(string $configKey): bool;

    /**
     * Returns the available config definitions.
     *
     * @return \Netgen\BlockManager\Config\ConfigDefinitionInterface[]
     */
    public function getConfigDefinitions(): array;
}
