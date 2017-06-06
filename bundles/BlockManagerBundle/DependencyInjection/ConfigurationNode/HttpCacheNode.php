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
                ->integerNode('shared_max_age')
                    ->min(0)
                ->end()
            ->end();

        return $node;
    }
}
