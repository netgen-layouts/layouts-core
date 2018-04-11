<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\Stubs;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ExtensionPlugin as BaseExtensionPlugin;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

final class ExtensionPlugin extends BaseExtensionPlugin
{
    public function addConfiguration(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->scalarNode('test_config')
                    ->defaultValue('test')
                    ->cannotBeEmpty()
                ->end()
            ->end();
    }

    public function appendConfigurationFiles()
    {
        return array(
            __DIR__ . '/block_types.yml',
        );
    }
}
