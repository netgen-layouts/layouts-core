<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNodeInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

final class HttpCacheNode implements ConfigurationNodeInterface
{
    public function getConfigurationNode(): NodeDefinition
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
     */
    private function configureTtlNode(NodeDefinition $node): NodeDefinition
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
