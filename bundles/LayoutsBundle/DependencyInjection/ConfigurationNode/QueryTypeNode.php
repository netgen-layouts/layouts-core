<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNodeInterface;
use Netgen\Layouts\Utils\BackwardsCompatibility\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

final class QueryTypeNode implements ConfigurationNodeInterface
{
    public function getConfigurationNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('query_types');
        $node = $treeBuilder->getRootNode();

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('query_type')
            ->arrayPrototype()
                ->canBeDisabled()
                ->children()
                    ->scalarNode('name')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->integerNode('priority')
                        ->defaultValue(0)
                    ->end()
                    ->scalarNode('handler')
                        ->cannotBeEmpty()
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}
