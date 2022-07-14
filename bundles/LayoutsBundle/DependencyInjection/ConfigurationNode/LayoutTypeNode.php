<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNodeInterface;
use Netgen\Layouts\Utils\BackwardsCompatibility\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

use function array_unique;
use function array_values;
use function is_string;

final class LayoutTypeNode implements ConfigurationNodeInterface
{
    public function getConfigurationNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('layout_types');
        $node = $treeBuilder->getRootNode();

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('layout_type')
            ->arrayPrototype()
                ->canBeDisabled()
                ->children()
                    ->scalarNode('name')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('icon')
                        ->defaultValue(null)
                        ->validate()
                            ->ifTrue(
                                static fn ($v): bool => !($v === null || (is_string($v) && $v !== '')),
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
                                ->scalarNode('name')
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
                                    ->scalarPrototype()
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
