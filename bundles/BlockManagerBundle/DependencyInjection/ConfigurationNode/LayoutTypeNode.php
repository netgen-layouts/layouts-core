<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNodeInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

final class LayoutTypeNode implements ConfigurationNodeInterface
{
    public function getConfigurationNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('layout_types');

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('type')
            ->prototype('array')
                ->canBeDisabled()
                ->children()
                    ->scalarNode('name')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('icon')
                        ->defaultValue(null)
                        ->validate()
                            ->ifTrue(function ($v): bool {
                                return !($v === null || (is_string($v) && !empty($v)));
                            })
                            ->thenInvalid('Icon path needs to be a non empty string or null.')
                        ->end()
                    ->end()
                    ->arrayNode('zones')
                        ->isRequired()
                        ->performNoDeepMerging()
                        ->requiresAtLeastOneElement()
                        ->prototype('array')
                            ->children()
                                ->scalarNode('name')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                                ->arrayNode('allowed_block_definitions')
                                    ->validate()
                                        ->always(function (array $v): array {
                                            return array_values(array_unique($v));
                                        })
                                    ->end()
                                    ->requiresAtLeastOneElement()
                                    ->prototype('scalar')
                                        ->cannotBeEmpty()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}
