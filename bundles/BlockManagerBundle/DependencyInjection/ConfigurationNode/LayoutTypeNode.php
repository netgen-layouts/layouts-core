<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNodeInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class LayoutTypeNode implements ConfigurationNodeInterface
{
    /**
     * Returns node definition for layouts.
     *
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    public function getConfigurationNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('layout_types');

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('type')
            ->prototype('array')
                ->canBeDisabled()
                ->children()
                    ->scalarNode('name')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->arrayNode('zones')
                        ->isRequired()
                        ->performNoDeepMerging()
                        ->requiresAtLeastOneElement()
                        ->prototype('array')
                            ->children()
                                ->scalarNode('name')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                                ->arrayNode('allowed_block_definitions')
                                    ->validate()
                                        ->always(function ($v) {
                                            return array_values(array_unique($v));
                                        })
                                    ->end()
                                    ->requiresAtLeastOneElement()
                                    ->prototype('scalar')
                                        ->cannotBeEmpty()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}
