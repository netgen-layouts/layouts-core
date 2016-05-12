<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @var string
     */
    protected $alias;

    /**
     * @var \Closure[]
     */
    protected $externalConfigTreeBuilders = array();

    /**
     * Constructor.
     *
     * @param string $alias
     * @param \Closure[] $externalConfigTreeBuilders
     */
    public function __construct($alias, array $externalConfigTreeBuilders = array())
    {
        $this->alias = $alias;
        $this->externalConfigTreeBuilders = $externalConfigTreeBuilders;
    }

    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root($this->alias);
        $children = $rootNode->children();

        foreach ($this->getAvailableNodeDefinitions() as $nodeDefinition) {
            $children->append($nodeDefinition);
        }

        foreach ($this->externalConfigTreeBuilders as $externalConfigTreeBuilder) {
            $externalConfigTreeBuilder($rootNode, $this);
        }

        $children->end();

        return $treeBuilder;
    }

    /**
     * Returns various semantic configuration for the bundle.
     *
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition[]
     */
    public function getAvailableNodeDefinitions()
    {
        return array(
            $this->getTemplateResolverNodeDefinition('block_view'),
            $this->getTemplateResolverNodeDefinition('layout_view'),
            $this->getBlockDefinitionsNodeDefinition(),
            $this->getBlockTypesNodeDefinition(),
            $this->getBlockTypeGroupsNodeDefinition(),
            $this->getLayoutsNodeDefinition(),
            $this->getSourcesNodeDefinition(),
            $this->getPagelayoutNodeDefinition(),
        );
    }

    /**
     * Returns node definition for template resolvers.
     *
     * @param string $nodeName
     *
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    public function getTemplateResolverNodeDefinition($nodeName)
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root($nodeName);

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('context')
            ->prototype('array')
                ->useAttributeAsKey('config')
                ->requiresAtLeastOneElement()
                ->prototype('array')
                    ->children()
                        ->scalarNode('template')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->arrayNode('match')
                            ->isRequired()
                            ->prototype('scalar')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }

    /**
     * Returns node definition for block definitions.
     *
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    public function getBlockDefinitionsNodeDefinition()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('block_definitions');

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('identifier')
            ->prototype('array')
                ->children()
                    ->arrayNode('forms')
                        ->isRequired()
                        ->children()
                            ->scalarNode('edit')
                                ->cannotBeEmpty()
                                ->defaultValue('block_edit')
                            ->end()
                            ->scalarNode('inline_edit')
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('view_types')
                        ->isRequired()
                        ->performNoDeepMerging()
                        ->requiresAtLeastOneElement()
                        ->useAttributeAsKey('view_type')
                        ->prototype('array')
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

    /**
     * Returns node definition for block types.
     *
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    public function getBlockTypesNodeDefinition()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('block_types');

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('identifier')
            ->prototype('array')
                ->children()
                    ->scalarNode('name')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->arrayNode('defaults')
                        ->isRequired()
                        ->children()
                            ->scalarNode('name')
                                ->defaultValue('')
                            ->end()
                            ->scalarNode('view_type')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('definition_identifier')
                                ->isRequired()
                                ->cannotBeEmpty()
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

    /**
     * Returns node definition for block type groups.
     *
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    public function getBlockTypeGroupsNodeDefinition()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('block_type_groups');

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('identifier')
            ->prototype('array')
                ->children()
                    ->scalarNode('name')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->arrayNode('block_types')
                        ->validate()
                            ->always(function ($v) {
                                return array_values(array_unique($v));
                            })
                        ->end()
                        ->requiresAtLeastOneElement()
                        ->prototype('scalar')
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }

    /**
     * Returns node definition for layouts.
     *
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    public function getLayoutsNodeDefinition()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('layouts');

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('type')
            ->prototype('array')
                ->children()
                    ->scalarNode('name')
                        ->isRequired()
                        ->cannotBeEmpty()
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
                                ->arrayNode('allowed_block_types')
                                    ->validate()
                                        ->always(function ($v) {
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

    /**
     * Returns node definition for sources.
     *
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    public function getSourcesNodeDefinition()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('sources');

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('source')
            ->prototype('array')
                ->beforeNormalization()
                    ->ifTrue(function ($v) { return is_array($v) && !array_key_exists('queries', $v); })
                    ->then(function ($v) {
                        // Key that should not be rewritten to the query config
                        $excludedKeys = array('name' => true);
                        $query = array();
                        foreach ($v as $key => $value) {
                            if (isset($excludedKeys[$key])) {
                                continue;
                            }
                            $query[$key] = $v[$key];
                            unset($v[$key]);
                        }
                        $v['queries'] = array('default' => $query);

                        return $v;
                    })
                ->end()
                ->children()
                    ->scalarNode('name')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->arrayNode('queries')
                        ->isRequired()
                        ->performNoDeepMerging()
                        ->requiresAtLeastOneElement()
                        ->prototype('array')
                            ->children()
                                ->scalarNode('query_type')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                                ->arrayNode('default_parameters')
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
                ->end()
            ->end();

        return $node;
    }

    /**
     * Returns node definition for pagelayout.
     *
     * @return \Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition
     */
    public function getPagelayoutNodeDefinition()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('pagelayout', 'scalar');

        $node
            ->defaultValue('NetgenBlockManagerBundle::pagelayout_empty.html.twig')
            ->cannotBeEmpty();

        return $node;
    }
}
