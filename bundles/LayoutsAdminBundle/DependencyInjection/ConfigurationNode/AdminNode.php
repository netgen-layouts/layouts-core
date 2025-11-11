<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNodeInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

final class AdminNode implements ConfigurationNodeInterface
{
    public function getConfigurationNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('admin');
        $node = $treeBuilder->getRootNode();

        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('javascripts')
                    ->requiresAtLeastOneElement()
                    ->stringPrototype()
                        ->cannotBeEmpty()
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
