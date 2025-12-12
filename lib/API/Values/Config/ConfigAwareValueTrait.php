<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Config;

use Netgen\Layouts\Exception\API\ConfigException;

trait ConfigAwareValueTrait
{
    public private(set) ConfigList $configs {
        get => new ConfigList($this->configs->toArray());
    }

    final public function getConfig(string $configKey): Config
    {
        return $this->configs->get($configKey) ??
            throw ConfigException::noConfig($configKey);
    }

    final public function hasConfig(string $configKey): bool
    {
        return $this->configs->containsKey($configKey);
    }
}
