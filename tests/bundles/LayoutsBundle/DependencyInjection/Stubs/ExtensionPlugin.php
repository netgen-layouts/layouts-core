<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\Stubs;

use Netgen\Bundle\LayoutsBundle\DependencyInjection\ExtensionPlugin as BaseExtensionPlugin;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

final class ExtensionPlugin extends BaseExtensionPlugin
{
    public function addConfiguration(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->scalarNode('test_config')
                    ->defaultValue('test')
                    ->cannotBeEmpty();
    }

    public function appendConfigurationFiles(): array
    {
        return [
            __DIR__ . '/block_types.yaml',
        ];
    }
}
