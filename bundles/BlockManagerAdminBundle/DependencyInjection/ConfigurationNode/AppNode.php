<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNodeInterface;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

final class AppNode implements ConfigurationNodeInterface
{
    public function getConfigurationNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('app');
        $node = $treeBuilder->getRootNode();

        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('javascripts')
                    ->prototype('scalar')
                        ->cannotBeEmpty()
                        ->validate()
                            ->ifTrue(
                                static function ($v): bool {
                                    return !is_string($v);
                                }
                            )
                            ->thenInvalid('The value should be a string')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('stylesheets')
                    ->prototype('scalar')
                        ->cannotBeEmpty()
                        ->validate()
                            ->ifTrue(
                                static function ($v): bool {
                                    return !is_string($v);
                                }
                            )
                            ->thenInvalid('The value should be a string')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}
