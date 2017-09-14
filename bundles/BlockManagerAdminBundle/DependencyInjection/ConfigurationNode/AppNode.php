<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNodeInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class AppNode implements ConfigurationNodeInterface
{
    public function getConfigurationNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('app');

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
