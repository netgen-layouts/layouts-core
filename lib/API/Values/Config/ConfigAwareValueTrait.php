<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Config;

use Netgen\Layouts\Exception\API\ConfigException;

trait ConfigAwareValueTrait
{
    public private(set) ConfigList $configs {
        get => new ConfigList($this->configs->toArray());
    }

    /**
     * Returns the config with specified config key.
     *
     * @throws \Netgen\Layouts\Exception\API\ConfigException If the config does not exist
     */
    public function getConfig(string $configKey): Config
    {
        return $this->configs->get($configKey) ??
            throw ConfigException::noConfig($configKey);
    }

    /**
     * Returns if the config with specified config key exists.
     */
    public function hasConfig(string $configKey): bool
    {
        return $this->configs->containsKey($configKey);
    }
}
