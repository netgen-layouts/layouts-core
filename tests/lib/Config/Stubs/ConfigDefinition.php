<?php

namespace Netgen\BlockManager\Tests\Config\Stubs;

use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\Config\ConfigDefinitionInterface;
use Netgen\BlockManager\Exception\InvalidArgumentException;

class ConfigDefinition implements ConfigDefinitionInterface
{
    /**
     * @var string
     */
    protected $configKey;

    /**
     * @var \Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinitionHandler
     */
    protected $handler;

    /**
     * Constructor.
     *
     * @param string $configKey
     * @param \Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinitionHandler $handler
     */
    public function __construct($configKey, ConfigDefinitionHandler $handler)
    {
        $this->configKey = $configKey;
        $this->handler = $handler;
    }

    /**
     * Returns config definition config key.
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

    /**
     * Returns the list of parameters in the object.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        return $this->handler->getParameters();
    }

    /**
     * Returns the parameter with provided name.
     *
     * @param string $parameterName
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If parameter with provided name does not exist
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface
     */
    public function getParameter($parameterName)
    {
        if ($this->hasParameter($parameterName)) {
            return $this->handler->getParameters()[$parameterName];
        }

        throw new InvalidArgumentException('parameterName', 'Parameter is missing.');
    }

    /**
     * Returns if the parameter with provided name exists in the collection.
     *
     * @param string $parameterName
     *
     * @return bool
     */
    public function hasParameter($parameterName)
    {
        return isset($this->handler->getParameters()[$parameterName]);
    }
}
