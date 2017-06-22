<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNodeInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class ApiKeysNode implements ConfigurationNodeInterface
{
    /**
     * Returns node definition for Google Maps.
     *
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    public function getConfigurationNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('api_keys');

        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('google_maps')
                ->defaultValue('')
            ->end();

        return $node;
    }
}
