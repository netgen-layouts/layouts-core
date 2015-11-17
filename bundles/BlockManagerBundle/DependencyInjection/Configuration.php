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
    public function __construct($alias, array $externalConfigTreeBuilders)
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
            $this->getBlocksNodeDefinition(),
            $this->getBlockGroupsNodeDefinition(),
            $this->getLayoutsNodeDefinition(),
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
    protected function getTemplateResolverNodeDefinition($nodeName)
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
     * Returns node definition for blocks.
     *
     * @param string $nodeName
     *
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    protected function getBlocksNodeDefinition($nodeName = 'blocks')
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root($nodeName);

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('identifier')
            ->prototype('array')
                ->children()
                    ->scalarNode('name')
                        ->isRequired()
                        ->cannotBeEmpty()
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
     * Returns node definition for blocks.
     *
     * @param string $nodeName
     *
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    protected function getBlockGroupsNodeDefinition($nodeName = 'block_groups')
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root($nodeName);

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('identifier')
            ->prototype('array')
                ->children()
                    ->scalarNode('name')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->arrayNode('blocks')
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
     * @param string $nodeName
     *
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    protected function getLayoutsNodeDefinition($nodeName = 'layouts')
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root($nodeName);

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('identifier')
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
                                ->arrayNode('allowed_blocks')
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
     * Returns node definition for pagelayout.
     *
     * @param string $nodeName
     *
     * @return \Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition
     */
    protected function getPagelayoutNodeDefinition($nodeName = 'pagelayout')
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root($nodeName, 'scalar');

        $node
            ->defaultValue('NetgenBlockManagerBundle::pagelayout_empty.html.twig')
            ->cannotBeEmpty();

        return $node;
    }
}
