<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNodeInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

use function array_unique;
use function array_values;
use function is_string;

final class LayoutTypeNode implements ConfigurationNodeInterface
{
    /**
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition<\Symfony\Component\Config\Definition\Builder\NodeParentInterface>
     */
    public function getConfigurationNode(): ArrayNodeDefinition
    {
        $treeBuilder = new TreeBuilder('layout_types');
        $node = $treeBuilder->getRootNode();

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('layout_type')
            ->arrayPrototype()
                ->canBeDisabled()
                ->children()
                    ->stringNode('name')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('icon')
                        ->defaultValue(null)
                        ->validate()
                            ->ifTrue(
                                static fn (mixed $v): bool => !($v === null || (is_string($v) && $v !== '')),
                            )
                            ->thenInvalid('Icon path needs to be a non empty string or null.')
                        ->end()
                    ->end()
                    ->arrayNode('zones')
                        ->isRequired()
                        ->performNoDeepMerging()
                        ->requiresAtLeastOneElement()
                        ->arrayPrototype()
                            ->children()
                                ->stringNode('name')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                                ->arrayNode('allowed_block_definitions')
                                    ->validate()
                                        ->always(
                                            static fn (array $v): array => array_values(array_unique($v)),
                                        )
                                    ->end()
                                    ->requiresAtLeastOneElement()
                                    ->stringPrototype()
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
