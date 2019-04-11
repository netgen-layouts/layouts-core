<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\DependencyInjection;

use Netgen\Bundle\LayoutsBundle\DependencyInjection\ExtensionPlugin as BaseExtensionPlugin;

final class ExtensionPlugin extends BaseExtensionPlugin
{
    protected function getConfigurationNodes(): array
    {
        return [
            new ConfigurationNode\AdminNode(),
            new ConfigurationNode\AppNode(),
        ];
    }
}
