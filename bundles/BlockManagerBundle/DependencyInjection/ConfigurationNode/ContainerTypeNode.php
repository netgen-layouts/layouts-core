<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNodeInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class ContainerTypeNode implements ConfigurationNodeInterface
{
    /**
     * Returns node definition for container types.
     *
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    public function getConfigurationNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('container_types');

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('identifier')
            ->prototype('array')
                ->canBeDisabled()
                ->validate()
                    ->always(function ($v) {
                        if (isset($v['enabled']) && !$v['enabled']) {
                            return $v;
                        }

                        if (isset($v['definition_identifier']) && !isset($v['name'])) {
                            throw new InvalidConfigurationException(
                                'You must specify container type name if you specify container definition'
                            );
                        }

                        return $v;
                    })
                ->end()
                ->children()
                    ->scalarNode('name')
                        ->cannotBeEmpty()
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
                            ->arrayNode('parameters')
                                ->defaultValue(array())
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
