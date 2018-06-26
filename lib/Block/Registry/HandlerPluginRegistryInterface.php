<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Block\Registry;

interface HandlerPluginRegistryInterface
{
    /**
     * Returns all handler plugins for the provided handler class.
     *
     * @param string $handlerClass
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Handler\PluginInterface[]
     */
    public function getPlugins(string $handlerClass): array;
}
