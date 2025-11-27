<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Config;

interface ConfigAwareStruct
{
    /**
     * Returns all config structs from the struct.
     *
     * @var \Netgen\Layouts\API\Values\Config\ConfigStruct[]
     */
    public array $configStructs { get; }

    /**
     * Sets the config struct to this struct.
     */
    public function setConfigStruct(string $configKey, ConfigStruct $configStruct): void;

    /**
     * Returns if the struct has a config struct with provided config key.
     */
    public function hasConfigStruct(string $configKey): bool;

    /**
     * Gets the config struct with provided config key.
     *
     * @throws \Netgen\Layouts\Exception\API\ConfigException If config struct does not exist
     */
    public function getConfigStruct(string $configKey): ConfigStruct;
}
