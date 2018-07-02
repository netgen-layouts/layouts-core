<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNodeInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

final class BlockTypeNode implements ConfigurationNodeInterface
{
    public function getConfigurationNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('block_types');

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('identifier')
            ->prototype('array')
                ->canBeDisabled()
                ->validate()
                    ->always(function (array $v): array {
                        if (isset($v['enabled']) && !$v['enabled']) {
                            return $v;
                        }

                        if (isset($v['definition_identifier']) && !isset($v['name'])) {
                            throw new InvalidConfigurationException(
                                'You must specify block type name if you specify block definition'
                            );
                        }

                        return $v;
                    })
                ->end()
                ->children()
                    ->scalarNode('name')
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
                                ->prototype('variable')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}
