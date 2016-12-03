<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\Stubs;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ExtensionPlugin as BaseExtensionPlugin;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

class ExtensionPlugin extends BaseExtensionPlugin
{
    /**
     * Processes the configuration for the bundle.
     *
     * @param \Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    public function addConfiguration(NodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->scalarNode('test_config')
                    ->defaultValue('test')
                    ->cannotBeEmpty()
                ->end()
            ->end();
    }

    /**
     * Returns the array of files to be appended to main bundle configuration.
     *
     * @return array
     */
    public function appendConfigurationFiles()
    {
        return array(
            __DIR__ . '/block_types.yml',
        );
    }
}
