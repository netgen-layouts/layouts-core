<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ExtensionPlugin as BaseExtensionPlugin;

class ExtensionPlugin extends BaseExtensionPlugin
{
    /**
     * Returns available configuration nodes for the bundle.
     *
     * @return \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNodeInterface[]
     */
    public function getConfigurationNodes()
    {
        return array(
            new ConfigurationNode\AdminNode(),
            new ConfigurationNode\AppNode(),
        );
    }
}
