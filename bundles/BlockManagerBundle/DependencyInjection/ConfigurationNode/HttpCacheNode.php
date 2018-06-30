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

        $node
            ->children()
                ->arrayNode('invalidation')
                    ->canBeDisabled()
                    ->addDefaultsIfNotSet()
                ->end()
            ->end();

        return $node;
    }
}
