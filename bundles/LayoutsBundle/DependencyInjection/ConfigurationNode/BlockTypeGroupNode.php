<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNodeInterface;
use Netgen\Layouts\Utils\BackwardsCompatibility\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

final class BlockTypeGroupNode implements ConfigurationNodeInterface
{
    public function getConfigurationNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('block_type_groups');
        $node = $treeBuilder->getRootNode();

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('block_type_group')
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
                    ->arrayNode('block_types')
                        ->requiresAtLeastOneElement()
                        ->arrayPrototype()
                            ->beforeNormalization()
                                ->ifString()
                                ->then(static fn (string $v): array => ['identifier' => $v, 'priority' => 0])
                            ->end()
                            ->children()
                                ->scalarNode('identifier')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                                ->integerNode('priority')
                                    ->defaultValue(0)
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}
