<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNodeInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

use function is_string;

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
                    ->requiresAtLeastOneElement()
                    ->stringPrototype()
                        ->cannotBeEmpty()
                        ->validate()
                            ->ifTrue(
                                static fn ($v): bool => !is_string($v),
                            )
                            ->thenInvalid('The value should be a string')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('stylesheets')
                    ->requiresAtLeastOneElement()
                    ->stringPrototype()
                        ->cannotBeEmpty()
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}
