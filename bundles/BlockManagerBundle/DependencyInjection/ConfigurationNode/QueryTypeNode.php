<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode;

use Netgen\BlockManager\Collection\Query\Form\FullEditType;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNodeInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class QueryTypeNode implements ConfigurationNodeInterface
{
    /**
     * Returns node definition for query types.
     *
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    public function getConfigurationNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('query_types');

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('identifier')
            ->prototype('array')
                ->children()
                    ->scalarNode('name')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('handler')
                        ->cannotBeEmpty()
                    ->end()
                    ->arrayNode('forms')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->arrayNode('full')
                                ->canBeDisabled()
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('type')
                                        ->treatNullLike(FullEditType::class)
                                        ->defaultValue(FullEditType::class)
                                        ->cannotBeEmpty()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('defaults')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->arrayNode('parameters')
                                ->defaultValue(array())
                                ->performNoDeepMerging()
                                ->requiresAtLeastOneElement()
                                ->useAttributeAsKey('parameter')
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
