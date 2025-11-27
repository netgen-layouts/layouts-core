<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNodeInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

final class QueryTypeNode implements ConfigurationNodeInterface
{
    /**
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition<\Symfony\Component\Config\Definition\Builder\NodeParentInterface>
     */
    public function getConfigurationNode(): ArrayNodeDefinition
    {
        $treeBuilder = new TreeBuilder('query_types');
        $node = $treeBuilder->getRootNode();

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('query_type')
            ->arrayPrototype()
                ->canBeDisabled()
                ->children()
                    ->stringNode('name')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->integerNode('priority')
                        ->defaultValue(0)
                    ->end()
                    ->stringNode('handler')
                        ->cannotBeEmpty()
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}
