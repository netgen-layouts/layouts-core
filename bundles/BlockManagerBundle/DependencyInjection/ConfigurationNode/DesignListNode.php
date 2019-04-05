<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNodeInterface;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

final class DesignListNode implements ConfigurationNodeInterface
{
    public function getConfigurationNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('design_list');
        $node = $treeBuilder->getRootNode();

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('design_name')
            ->arrayPrototype()
                ->scalarPrototype()
                    ->cannotBeEmpty()
                ->end()
            ->end();

        return $node;
    }
}
