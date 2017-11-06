<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNodeInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

final class DesignListNode implements ConfigurationNodeInterface
{
    public function getConfigurationNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('design_list');

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('design_name')
            ->prototype('array')
                ->requiresAtLeastOneElement()
                ->prototype('scalar')
                    ->cannotBeEmpty()
                ->end()
            ->end();

        return $node;
    }
}
