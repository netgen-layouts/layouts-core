<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Config;

use Netgen\Layouts\Exception\API\ConfigException;

use function array_key_exists;

trait ConfigAwareStructTrait
{
    public private(set) array $configStructs = [];

    final public function setConfigStruct(string $configKey, ConfigStruct $configStruct): void
    {
        $this->configStructs[$configKey] = $configStruct;
    }

    final public function hasConfigStruct(string $configKey): bool
    {
        return array_key_exists($configKey, $this->configStructs);
    }

    final public function getConfigStruct(string $configKey): ConfigStruct
    {
        if (!$this->hasConfigStruct($configKey)) {
            throw ConfigException::noConfigStruct($configKey);
        }

        return $this->configStructs[$configKey];
    }
}
