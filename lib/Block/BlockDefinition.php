<?php

namespace Netgen\BlockManager\Block;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Parameters\ParameterCollectionTrait;
use Netgen\BlockManager\ValueObject;

class BlockDefinition extends ValueObject implements BlockDefinitionInterface
{
    use ParameterCollectionTrait;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface
     */
    protected $handler;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Handler\PluginInterface[]
     */
    protected $handlerPlugins = array();

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration
     */
    protected $config;

    /**
     * @var \Netgen\BlockManager\Config\ConfigDefinitionInterface[]
     */
    protected $configDefinitions = array();

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function getDynamicParameters(Block $block)
    {
        $dynamicParams = new DynamicParameters();

        $this->handler->getDynamicParameters($dynamicParams, $block);

        foreach ($this->handlerPlugins as $handlerPlugin) {
            $handlerPlugin->getDynamicParameters($dynamicParams, $block);
        }

        return $dynamicParams;
    }

    public function isContextual(Block $block)
    {
        return $this->handler->isContextual($block);
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getConfigDefinitions()
    {
        return $this->configDefinitions;
    }

    public function hasPlugin($className)
    {
        foreach ($this->handlerPlugins as $handlerPlugin) {
            if (is_a($handlerPlugin, $className, true)) {
                return true;
            }
        }

        return false;
    }
}
