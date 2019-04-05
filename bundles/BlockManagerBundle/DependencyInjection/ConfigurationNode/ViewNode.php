<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNodeInterface;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

final class ViewNode implements ConfigurationNodeInterface
{
    public function getConfigurationNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('view');
        $node = $treeBuilder->getRootNode();

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('view')
            ->arrayPrototype()
                ->requiresAtLeastOneElement()
                ->useAttributeAsKey('context')
                ->arrayPrototype()
                    ->useAttributeAsKey('config')
                    ->requiresAtLeastOneElement()
                    ->arrayPrototype()
                        ->performNoDeepMerging()
                        ->children()
                            ->scalarNode('template')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->arrayNode('match')
                                ->isRequired()
                                ->variablePrototype()
                                ->end()
                            ->end()
                            ->arrayNode('parameters')
                                ->defaultValue([])
                                ->variablePrototype()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}
