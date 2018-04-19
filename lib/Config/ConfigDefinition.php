<?php

namespace Netgen\BlockManager\Config;

use Netgen\BlockManager\Parameters\ParameterCollectionTrait;
use Netgen\BlockManager\Value;

/**
 * @final
 */
class ConfigDefinition extends Value implements ConfigDefinitionInterface
{
    use ParameterCollectionTrait;

    /**
     * @var string
     */
    protected $configKey;

    /**
     * @var \Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface
     */
    protected $handler;

    public function getConfigKey()
    {
        return $this->configKey;
    }
}
