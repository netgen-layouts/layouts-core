<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Config;

use Netgen\Layouts\API\Values\Config\Config as APIConfig;
use Netgen\Layouts\Exception\API\ConfigException;

trait ConfigAwareValueTrait
{
    /**
     * @var \Netgen\Layouts\API\Values\Config\Config[]
     */
    private $configs = [];

    /**
     * Returns all available configs.
     */
    public function getConfigs(): ConfigList
    {
        return new ConfigList($this->configs);
    }

    /**
     * Returns the config with specified config key.
     *
     * @throws \Netgen\Layouts\Exception\API\ConfigException If the config does not exist
     */
    public function getConfig(string $configKey): APIConfig
    {
        if ($this->hasConfig($configKey)) {
            return $this->configs[$configKey];
        }

        throw ConfigException::noConfig($configKey);
    }

    /**
     * Returns if the config with specified config key exists.
     */
    public function hasConfig(string $configKey): bool
    {
        return array_key_exists($configKey, $this->configs);
    }
}
