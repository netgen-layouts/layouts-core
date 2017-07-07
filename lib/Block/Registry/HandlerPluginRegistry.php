<?php

namespace Netgen\BlockManager\Block\Registry;

use Netgen\BlockManager\Block\BlockDefinition\Handler\PluginInterface;

class HandlerPluginRegistry implements HandlerPluginRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface[]
     */
    protected $handlerPlugins = array();

    /**
     * Adds a block definition to registry.
     *
     * @param \Netgen\BlockManager\Block\BlockDefinition\Handler\PluginInterface $plugin
     */
    public function addPlugin(PluginInterface $plugin)
    {
        $this->handlerPlugins[] = $plugin;
    }

    /**
     * Returns all plugins for the provided handler class.
     *
     * @param string $handlerClass
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Handler\PluginInterface[]
     */
    public function getPlugins($handlerClass)
    {
        return array_values(
            array_filter(
                $this->handlerPlugins,
                function (PluginInterface $plugin) use ($handlerClass) {
                    return is_a($handlerClass, $plugin::getExtendedHandler(), true);
                }
            )
        );
    }
}
