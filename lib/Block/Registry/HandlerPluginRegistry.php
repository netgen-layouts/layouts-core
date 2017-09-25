<?php

namespace Netgen\BlockManager\Block\Registry;

use Netgen\BlockManager\Block\BlockDefinition\Handler\PluginInterface;

final class HandlerPluginRegistry implements HandlerPluginRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Handler\PluginInterface[]
     */
    private $handlerPlugins = array();

    public function addPlugin(PluginInterface $plugin)
    {
        $this->handlerPlugins[] = $plugin;
    }

    public function getPlugins($handlerClass)
    {
        return array_values(
            array_filter(
                $this->handlerPlugins,
                function (PluginInterface $plugin) use ($handlerClass) {
                    $extendedHandlers = (array) $plugin::getExtendedHandler();
                    foreach ($extendedHandlers as $extendedHandler) {
                        if (is_a($handlerClass, $extendedHandler, true)) {
                            return true;
                        }
                    }

                    return false;
                }
            )
        );
    }
}
