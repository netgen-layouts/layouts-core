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
     * @var array
     */
    protected $availableParameters = array();

    /**
     * Constructor.
     *
     * @param string $alias
     */
    public function __construct($alias)
    {
        $this->alias = $alias;
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

        $children->append($this->getPageLayoutNodeDefinition());

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
        $this->availableParameters[] = $nodeName;

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
        $this->availableParameters[] = $nodeName;

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
                        ->validate()
                            ->always(function ($v) {
                                return array_values(array_unique($v));
                            })
                        ->end()
                        ->performNoDeepMerging()
                        ->defaultValue(array('default'))
                        ->prototype('scalar')
                            ->cannotBeEmpty()
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
        $this->availableParameters[] = $nodeName;

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
     * Returns node definition for pagelayout.
     *
     * @param string $nodeName
     *
     * @return \Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition
     */
    protected function getPageLayoutNodeDefinition($nodeName = 'pagelayout')
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root($nodeName, 'scalar');

        $node
            ->defaultValue('NetgenBlockManagerBundle::pagelayout_empty.html.twig')
            ->cannotBeEmpty();

        return $node;
    }

    /**
     * Returns parameters which are allowed to be used by other bundles when
     * building their semantic configuration. Useful for eZ Publish Block Manager
     * bundle, which uses this semantic config as a base for config resolver based
     * semantic config.
     *
     * @return array
     */
    public function getAvailableParameters()
    {
        return $this->availableParameters;
    }
}
