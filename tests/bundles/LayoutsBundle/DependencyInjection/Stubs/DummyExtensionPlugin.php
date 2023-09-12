<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\Stubs;

use Netgen\Bundle\LayoutsBundle\DependencyInjection\ExtensionPlugin as BaseExtensionPlugin;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

final class DummyExtensionPlugin extends BaseExtensionPlugin
{
    public function addConfiguration(ArrayNodeDefinition $rootNode): void {}

    public function appendConfigurationFiles(): array
    {
        return [];
    }
}
