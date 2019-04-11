<?php

declare(strict_types=1);

namespace Netgen\Layouts\Config;

interface ConfigDefinitionAwareInterface
{
    /**
     * Returns the config definition with provided config key.
     *
     * @throws \Netgen\Layouts\Exception\Config\ConfigDefinitionException if config definition does not exist
     */
    public function getConfigDefinition(string $configKey): ConfigDefinitionInterface;

    /**
     * Returns if the config definition with provided config key exists.
     */
    public function hasConfigDefinition(string $configKey): bool;

    /**
     * Returns the available config definitions.
     *
     * @return \Netgen\Layouts\Config\ConfigDefinitionInterface[]
     */
    public function getConfigDefinitions(): array;
}
