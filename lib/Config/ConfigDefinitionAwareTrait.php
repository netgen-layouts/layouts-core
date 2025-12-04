<?php

declare(strict_types=1);

namespace Netgen\Layouts\Config;

use Netgen\Layouts\Exception\Config\ConfigDefinitionException;

use function array_key_exists;

trait ConfigDefinitionAwareTrait
{
    /**
     * Returns the available config definitions.
     *
     * @var \Netgen\Layouts\Config\ConfigDefinitionInterface[]
     */
    final public protected(set) array $configDefinitions = [];

    /**
     * Returns the config definition with provided config key.
     *
     * @throws \Netgen\Layouts\Exception\Config\ConfigDefinitionException if config definition does not exist
     */
    final public function getConfigDefinition(string $configKey): ConfigDefinitionInterface
    {
        if (!$this->hasConfigDefinition($configKey)) {
            throw ConfigDefinitionException::noConfigDefinition($configKey);
        }

        return $this->configDefinitions[$configKey];
    }

    /**
     * Returns if the config definition with provided config key exists.
     */
    final public function hasConfigDefinition(string $configKey): bool
    {
        return array_key_exists($configKey, $this->configDefinitions);
    }
}
