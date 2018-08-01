<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNodeInterface;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

final class ValueTypeNode implements ConfigurationNodeInterface
{
    public function getConfigurationNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('value_types');
        $node = $treeBuilder->getRootNode();

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('value_type')
            ->prototype('array')
                ->canBeDisabled()
                ->children()
                    ->scalarNode('name')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}
