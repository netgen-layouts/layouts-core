<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Core\Values\Config;

use Netgen\BlockManager\API\Values\Config\Config as APIConfig;
use Netgen\BlockManager\Exception\Core\ConfigException;

trait ConfigAwareValueTrait
{
    /**
     * @var \Netgen\BlockManager\API\Values\Config\Config[]
     */
    protected $configs = [];

    /**
     * Returns all available configs.
     *
     * @return \Netgen\BlockManager\API\Values\Config\Config[]
     */
    public function getConfigs(): array
    {
        return $this->configs;
    }

    /**
     * Returns the config with specified config key.
     *
     * @throws \Netgen\BlockManager\Exception\Core\ConfigException If the config does not exist
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
