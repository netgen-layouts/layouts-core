<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating\Plugin;

use function array_filter;

final class PluginCollection
{
    /**
     * @param \Netgen\Bundle\LayoutsBundle\Templating\Plugin\PluginInterface[] $plugins
     */
    public function __construct(
        private string $pluginName,
        private array $plugins,
    ) {
        $this->plugins = array_filter(
            $this->plugins,
            static fn (PluginInterface $plugin): bool => true,
        );
    }

    /**
     * Returns the plugin name for this collection.
     */
    public function getPluginName(): string
    {
        return $this->pluginName;
    }

    /**
     * Returns all the plugins registered in the collection.
     *
     * @return \Netgen\Bundle\LayoutsBundle\Templating\Plugin\PluginInterface[]
     */
    public function getPlugins(): array
    {
        return $this->plugins;
    }
}
