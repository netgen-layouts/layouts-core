<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ExtensionPlugin as BaseExtensionPlugin;

final class ExtensionPlugin extends BaseExtensionPlugin
{
    public function getConfigurationNodes()
    {
        return [
            new ConfigurationNode\AdminNode(),
            new ConfigurationNode\AppNode(),
        ];
    }
}
