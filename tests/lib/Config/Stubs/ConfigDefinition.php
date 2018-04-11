<?php

namespace Netgen\BlockManager\Tests\Config\Stubs;

use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface;
use Netgen\BlockManager\Config\ConfigDefinitionInterface;
use Netgen\BlockManager\Exception\InvalidArgumentException;

final class ConfigDefinition implements ConfigDefinitionInterface
{
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
    }

    public function getConfigKey()
    {
        return $this->configKey;
    }

    public function isEnabled(ConfigAwareValue $configAwareValue)
    {
        return $this->handler->isEnabled($configAwareValue);
    }

    public function getParameterDefinitions()
    {
        return $this->handler->getParameterDefinitions();
    }

    public function getParameterDefinition($parameterName)
    {
        if ($this->hasParameterDefinition($parameterName)) {
            return $this->handler->getParameterDefinitions()[$parameterName];
        }

        throw new InvalidArgumentException('parameterName', 'Parameter is missing.');
    }

    public function hasParameterDefinition($parameterName)
    {
        return isset($this->handler->getParameterDefinitions()[$parameterName]);
    }
}
