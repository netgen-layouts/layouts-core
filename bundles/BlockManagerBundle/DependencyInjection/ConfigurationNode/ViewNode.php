<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNodeInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

final class ViewNode implements ConfigurationNodeInterface
{
    public function getConfigurationNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('view');

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('view')
            ->prototype('array')
                ->requiresAtLeastOneElement()
                ->useAttributeAsKey('context')
                ->prototype('array')
                    ->useAttributeAsKey('config')
                    ->requiresAtLeastOneElement()
                    ->prototype('array')
                        ->performNoDeepMerging()
                        ->children()
                            ->scalarNode('template')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->arrayNode('match')
                                ->isRequired()
                                ->prototype('scalar')
                                ->end()
                            ->end()
                            ->arrayNode('parameters')
                                ->defaultValue([])
                                ->prototype('variable')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}
