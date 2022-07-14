<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNodeInterface;
use Netgen\Layouts\Utils\BackwardsCompatibility\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

use function is_string;

final class BlockTypeNode implements ConfigurationNodeInterface
{
    public function getConfigurationNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('block_types');
        $node = $treeBuilder->getRootNode();

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('block_type')
            ->arrayPrototype()
                ->canBeDisabled()
                ->validate()
                    ->always(
                        static function (array $v): array {
                            if (!($v['enabled'] ?? true)) {
                                return $v;
                            }

                            if (isset($v['definition_identifier']) && !isset($v['name'])) {
                                throw new InvalidConfigurationException(
                                    'You must specify block type name if you specify block definition',
                                );
                            }

                            return $v;
                        },
                    )
                ->end()
                ->children()
                    ->scalarNode('name')
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
                    ->scalarNode('definition_identifier')
                        ->cannotBeEmpty()
                    ->end()
                    ->arrayNode('defaults')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('name')
                                ->defaultValue('')
                            ->end()
                            ->scalarNode('view_type')
                                ->defaultValue('')
                            ->end()
                            ->scalarNode('item_view_type')
                                ->defaultValue('')
                            ->end()
                            ->arrayNode('parameters')
                                ->defaultValue([])
                                ->performNoDeepMerging()
                                ->requiresAtLeastOneElement()
                                ->useAttributeAsKey('parameter')
                                ->variablePrototype()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}
