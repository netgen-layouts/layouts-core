<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNodeInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

final class AdminNode implements ConfigurationNodeInterface
{
    public function getConfigurationNode(): NodeDefinition
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
                            ->ifTrue(function ($v): bool {
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
                            ->ifTrue(function ($v): bool {
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
