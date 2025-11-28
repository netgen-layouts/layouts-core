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
        /**
         * Returns the plugin name for this collection.
         */
        public private(set) string $pluginName,
        /**
         * Returns all the plugins registered in the collection.
         *
         * @var \Netgen\Bundle\LayoutsBundle\Templating\Plugin\PluginInterface[]
         */
        public private(set) array $plugins,
    ) {
        $this->plugins = array_filter(
            $this->plugins,
            static fn (PluginInterface $plugin): bool => true,
        );
    }
}
