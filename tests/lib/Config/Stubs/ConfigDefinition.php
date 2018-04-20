<?php

namespace Netgen\BlockManager\Tests\Config\Stubs;

use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface;
use Netgen\BlockManager\Config\ConfigDefinitionInterface;
use Netgen\BlockManager\Parameters\ParameterDefinitionCollectionTrait;

final class ConfigDefinition implements ConfigDefinitionInterface
{
    use ParameterDefinitionCollectionTrait;

    /**
     * @var string
     */
    private $configKey;

    /**
     * @var \Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface
     */
    private $handler;

    public function __construct($configKey, ConfigDefinitionHandlerInterface $handler = null)
    {
        $this->configKey = $configKey;
        $this->handler = $handler ?: new ConfigDefinitionHandler();
        $this->parameterDefinitions = $this->handler->getParameterDefinitions();
    }

    public function getConfigKey()
    {
        return $this->configKey;
    }

    public function isEnabled(ConfigAwareValue $configAwareValue)
    {
        return $this->handler->isEnabled($configAwareValue);
    }
}
