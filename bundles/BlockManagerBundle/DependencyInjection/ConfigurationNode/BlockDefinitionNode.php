<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode;

use Netgen\BlockManager\Block\Form\ContentEditType;
use Netgen\BlockManager\Block\Form\DesignEditType;
use Netgen\BlockManager\Block\Form\FullEditType;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNodeInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class BlockDefinitionNode implements ConfigurationNodeInterface
{
    /**
     * Returns node definition for block definitions.
     *
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    public function getConfigurationNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('block_definitions');

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('identifier')
            ->prototype('array')
                ->canBeDisabled()
                ->children()
                    ->scalarNode('name')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->arrayNode('forms')
                        ->addDefaultsIfNotSet()
                        ->validate()
                            ->always(function ($v) {
                                $exception = new InvalidConfigurationException('Block definition must either have a full form or content and design forms.');

                                if ($v['full']['enabled'] && ($v['design']['enabled'] || $v['content']['enabled'])) {
                                    throw $exception;
                                }

                                if (!$v['full']['enabled']) {
                                    if ($v['design']['enabled'] && !$v['content']['enabled']) {
                                        throw $exception;
                                    }

                                    if (!$v['design']['enabled'] && $v['content']['enabled']) {
                                        throw $exception;
                                    }
                                }

                                return $v;
                            })
                        ->end()
                        ->children()
                            ->arrayNode('full')
                                ->addDefaultsIfNotSet()
                                ->canBeDisabled()
                                ->children()
                                    ->scalarNode('type')
                                        ->treatNullLike(FullEditType::class)
                                        ->defaultValue(FullEditType::class)
                                        ->cannotBeEmpty()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('design')
                                ->addDefaultsIfNotSet()
                                ->canBeEnabled()
                                ->children()
                                    ->scalarNode('type')
                                        ->treatNullLike(DesignEditType::class)
                                        ->defaultValue(DesignEditType::class)
                                        ->cannotBeEmpty()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('content')
                                ->addDefaultsIfNotSet()
                                ->canBeEnabled()
                                ->children()
                                    ->scalarNode('type')
                                        ->treatNullLike(ContentEditType::class)
                                        ->defaultValue(ContentEditType::class)
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
                                        'item_view_types' => array(),
                                        'valid_parameters' => null,
                                    );
                                })
                            ->end()
                            ->children()
                                ->scalarNode('name')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                                ->arrayNode('item_view_types')
                                    ->defaultValue(array('standard' => array('name' => 'Standard')))
                                    ->performNoDeepMerging()
                                    ->requiresAtLeastOneElement()
                                    ->useAttributeAsKey('item_view_type')
                                    ->prototype('array')
                                        ->children()
                                            ->scalarNode('name')
                                                ->isRequired()
                                                ->cannotBeEmpty()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                                ->variableNode('valid_parameters')
                                    ->defaultNull()
                                    ->validate()
                                        ->ifTrue(function ($v) {
                                            return $v !== null && !is_array($v);
                                        })
                                        ->thenInvalid('The value should be null or an array')
                                    ->end()
                                    ->validate()
                                        ->always(function ($v) {
                                            if (is_array($v)) {
                                                return array_values(array_unique($v));
                                            }

                                            return $v;
                                        })
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
