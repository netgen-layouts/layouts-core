<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNodeInterface;

class PageLayoutNode implements ConfigurationNodeInterface
{
    /**
     * Returns node definition for pagelayout.
     *
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    public function getConfigurationNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('pagelayout', 'scalar');

        $node
            ->defaultValue('NetgenBlockManagerBundle::empty_pagelayout.html.twig')
            ->cannotBeEmpty();

        return $node;
    }
}
