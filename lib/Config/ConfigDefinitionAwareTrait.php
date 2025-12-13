<?php

declare(strict_types=1);

namespace Netgen\Layouts\Config;

use Netgen\Layouts\Exception\Config\ConfigDefinitionException;

use function array_key_exists;

trait ConfigDefinitionAwareTrait
{
    public private(set) array $configDefinitions = [];

    final public function getConfigDefinition(string $configKey): ConfigDefinitionInterface
    {
        if (!$this->hasConfigDefinition($configKey)) {
            throw ConfigDefinitionException::noConfigDefinition($configKey);
        }

        return $this->configDefinitions[$configKey];
    }

    final public function hasConfigDefinition(string $configKey): bool
    {
        return array_key_exists($configKey, $this->configDefinitions);
    }
}
