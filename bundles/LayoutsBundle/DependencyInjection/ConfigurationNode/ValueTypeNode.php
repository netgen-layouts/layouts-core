<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNodeInterface;
use Netgen\Layouts\Utils\BackwardsCompatibility\TreeBuilder;
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
            ->arrayPrototype()
                ->canBeDisabled()
                ->children()
                    ->scalarNode('name')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->booleanNode('manual_items')
                        ->defaultTrue()
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}
