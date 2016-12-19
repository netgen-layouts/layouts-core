<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode;

use Netgen\BlockManager\Layout\Container\Form\ContainerEditType;
use Netgen\BlockManager\Layout\Container\Form\PlaceholderEditType;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNodeInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class ContainerDefinitionNode implements ConfigurationNodeInterface
{
    /**
     * Returns node definition for container definitions.
     *
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    public function getConfigurationNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('container_definitions');

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('identifier')
            ->prototype('array')
                ->canBeDisabled()
                ->children()
                    ->scalarNode('handler')
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('name')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->arrayNode('forms')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->arrayNode('full')
                                ->canBeDisabled()
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('type')
                                        ->treatNullLike(ContainerEditType::class)
                                        ->defaultValue(ContainerEditType::class)
                                        ->cannotBeEmpty()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('placeholder_forms')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->arrayNode('full')
                                ->canBeDisabled()
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('type')
                                        ->treatNullLike(PlaceholderEditType::class)
                                        ->defaultValue(PlaceholderEditType::class)
                                        ->cannotBeEmpty()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('view_types')
                        ->isRequired()
                        ->requiresAtLeastOneElement()
                        ->useAttributeAsKey('view_type')
                        ->prototype('array')
                            ->canBeDisabled()
                            ->validate()
                                ->ifTrue(function ($v) {
                                    return $v['enabled'] !== true;
                                })
                                ->then(function ($v) {
                                    return array(
                                        'name' => 'Disabled',
                                        'enabled' => false,
                                    );
                                })
                            ->end()
                            ->children()
                                ->scalarNode('name')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}
