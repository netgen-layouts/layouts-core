<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Config;

use Netgen\Layouts\Exception\API\ConfigException;

use function array_key_exists;

trait ConfigAwareStructTrait
{
    /**
     * @var \Netgen\Layouts\API\Values\Config\ConfigStruct[]
     */
    private array $configStructs = [];

    /**
     * Sets the config struct to this struct.
     */
    public function setConfigStruct(string $configKey, ConfigStruct $configStruct): void
    {
        $this->configStructs[$configKey] = $configStruct;
    }

    /**
     * Returns if the struct has a config struct with provided config key.
     */
    public function hasConfigStruct(string $configKey): bool
    {
        return array_key_exists($configKey, $this->configStructs);
    }

    /**
     * Gets the config struct with provided config key.
     *
     * @throws \Netgen\Layouts\Exception\API\ConfigException If config struct does not exist
     */
    public function getConfigStruct(string $configKey): ConfigStruct
    {
        if (!$this->hasConfigStruct($configKey)) {
            throw ConfigException::noConfigStruct($configKey);
        }

        return $this->configStructs[$configKey];
    }

    /**
     * Returns all config structs from the struct.
     *
     * @return \Netgen\Layouts\API\Values\Config\ConfigStruct[]
     */
    public function getConfigStructs(): array
    {
        return $this->configStructs;
    }
}
