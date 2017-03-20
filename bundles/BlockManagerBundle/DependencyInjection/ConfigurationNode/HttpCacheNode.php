<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNodeInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class HttpCacheNode implements ConfigurationNodeInterface
{
    /**
     * Returns node definition for HTTP cache configuration.
     *
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    public function getConfigurationNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('http_cache')
            ->addDefaultsIfNotSet();

        $ttlNode = $node
            ->children()
                ->arrayNode('ttl')
                    ->addDefaultsIfNotSet();

        $defaultTtlNode = $ttlNode
            ->children()
                ->arrayNode('default')
                    ->addDefaultsIfNotSet();

        $this->configureTtlNode($defaultTtlNode->children()->arrayNode('layout'));

        $this->configureTtlNode(
            $ttlNode
                ->children()
                    ->arrayNode('layout_type')
                        ->useAttributeAsKey('layout_type')
                        ->prototype('array')
        );

        $this->configureTtlNode($defaultTtlNode->children()->arrayNode('block'));

        $this->configureTtlNode(
            $ttlNode
                ->children()
                    ->arrayNode('block_definition')
                        ->useAttributeAsKey('block_definition')
                        ->prototype('array')
        );

        $node
            ->children()
                ->arrayNode('invalidation')
                    ->canBeDisabled()
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('default_strategy')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->arrayNode('strategies')
                            ->isRequired()
                            ->requiresAtLeastOneElement()
                            ->useAttributeAsKey('identifier')
                            ->prototype('array')
                                ->children()
                                    ->arrayNode('block')
                                        ->isRequired()
                                        ->children()
                                            ->scalarNode('tagger')
                                                ->isRequired()
                                                ->cannotBeEmpty()
                                            ->end()
                                            ->scalarNode('invalidator')
                                                ->isRequired()
                                                ->cannotBeEmpty()
                                            ->end()
                                        ->end()
                                    ->end()
                                    ->arrayNode('layout')
                                        ->isRequired()
                                        ->children()
                                            ->scalarNode('tagger')
                                                ->isRequired()
                                                ->cannotBeEmpty()
                                            ->end()
                                            ->scalarNode('invalidator')
                                                ->isRequired()
                                                ->cannotBeEmpty()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }

    /**
     * Configures the node with TTL caching options.
     *
     * @param \Symfony\Component\Config\Definition\Builder\NodeDefinition $node
     *
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    protected function configureTtlNode($node)
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->integerNode('max_age')
                    ->min(0)
                ->end()
                ->integerNode('shared_max_age')
                    ->min(0)
                ->end()
                ->booleanNode('overwrite_headers')
                ->end()
            ->end();

        return $node;
    }
}
