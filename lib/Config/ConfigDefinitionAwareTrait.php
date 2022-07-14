<?php

declare(strict_types=1);

namespace Netgen\Layouts\Config;

use Netgen\Layouts\Exception\Config\ConfigDefinitionException;

use function array_key_exists;

trait ConfigDefinitionAwareTrait
{
    /**
     * @var \Netgen\Layouts\Config\ConfigDefinitionInterface[]
     */
    protected array $configDefinitions = [];

    /**
     * Returns the config definition with provided config key.
     *
     * @throws \Netgen\Layouts\Exception\Config\ConfigDefinitionException if config definition does not exist
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
     * @return \Netgen\Layouts\Config\ConfigDefinitionInterface[]
     */
    public function getConfigDefinitions(): array
    {
        return $this->configDefinitions;
    }
}
