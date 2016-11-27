<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNodeInterface;

class GoogleMapsNode implements ConfigurationNodeInterface
{
    /**
     * Returns node definition for Google Maps.
     *
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    public function getConfigurationNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('google_maps_api_key', 'scalar');

        $node->defaultValue('');

        return $node;
    }
}
