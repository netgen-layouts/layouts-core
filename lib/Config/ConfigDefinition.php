<?php

namespace Netgen\BlockManager\Config;

use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\Parameters\ParameterCollectionTrait;
use Netgen\BlockManager\ValueObject;

class ConfigDefinition extends ValueObject implements ConfigDefinitionInterface
{
    use ParameterCollectionTrait;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $configKey;

    /**
     * @var \Netgen\BlockManager\Config\ConfigDefinition\ConfigDefinitionHandlerInterface
     */
    protected $handler;

    /**
     * Returns the type of the config definition.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the config key for the definition.
     *
     * @return string
     */
    public function getConfigKey()
    {
        return $this->configKey;
    }

    /**
     * Returns if this config definition is enabled for current config aware value.
     *
     * @param \Netgen\BlockManager\API\Values\Config\ConfigAwareValue $configAwareValue
     *
     * @return bool
     */
    public function isEnabled(ConfigAwareValue $configAwareValue)
    {
        return $this->handler->isEnabled($configAwareValue);
    }
}
