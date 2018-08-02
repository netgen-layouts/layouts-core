<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Templating\Plugin;

final class PluginCollection
{
    /**
     * @var string
     */
    private $pluginName;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Plugin\PluginInterface[]
     */
    private $plugins;

    public function __construct(string $pluginName, array $plugins)
    {
        $this->pluginName = $pluginName;

        $this->plugins = array_filter(
            $plugins,
            function (PluginInterface $plugin): bool {
                return true;
            }
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
     * @return \Netgen\Bundle\BlockManagerBundle\Templating\Plugin\PluginInterface[]
     */
    public function getPlugins(): array
    {
        return $this->plugins;
    }
}
