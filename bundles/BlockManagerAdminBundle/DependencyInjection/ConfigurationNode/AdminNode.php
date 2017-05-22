<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNodeInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class AdminNode implements ConfigurationNodeInterface
{
    /**
     * Returns node definition for admin interface.
     *
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    public function getConfigurationNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('admin');

        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('javascripts')
                    ->prototype('scalar')
                        ->cannotBeEmpty()
                        ->validate()
                            ->ifTrue(function ($v) {
                                return !is_string($v);
                            })
                            ->thenInvalid('The value should be a string')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('stylesheets')
                    ->prototype('scalar')
                        ->cannotBeEmpty()
                        ->validate()
                            ->ifTrue(function ($v) {
                                return !is_string($v);
                            })
                            ->thenInvalid('The value should be a string')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}
