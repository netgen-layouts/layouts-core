<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Config;

interface ConfigAwareValue
{
    /**
     * Returns all available configs.
     */
    public function getConfigs(): ConfigList;

    /**
     * Returns the config with specified config key.
     *
     * @throws \Netgen\Layouts\Exception\API\ConfigException If the config does not exist
     */
    public function getConfig(string $configKey): Config;

    /**
     * Returns if the config with specified config key exists.
     */
    public function hasConfig(string $configKey): bool;
}
