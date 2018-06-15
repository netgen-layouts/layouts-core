<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Config;

interface ConfigAwareValue
{
    /**
     * Returns all available configs.
     *
     * @return \Netgen\BlockManager\API\Values\Config\Config[]
     */
    public function getConfigs(): array;

    /**
     * Returns the config with specified config key.
     *
     * @throws \Netgen\BlockManager\Exception\Core\ConfigException If the config does not exist
     */
    public function getConfig(string $configKey): Config;

    /**
     * Returns if the config with specified config key exists.
     */
    public function hasConfig(string $configKey): bool;
}
