<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\Stubs;

use Netgen\Bundle\LayoutsBundle\DependencyInjection\ExtensionPlugin;

final class EmptyExtensionPlugin extends ExtensionPlugin
{
    protected function getConfigurationNodes(): array
    {
        return [new ConfigurationNode(), new ConfigurationNode()];
    }
}
