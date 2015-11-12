<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
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

        $this->addConfiguration($children);
        $this->addPageLayoutNode($children);

        $children->end();
        return $treeBuilder;
    }

    /**
     * Adds various semantic configuration for the bundle
     *
     * @param \Symfony\Component\Config\Definition\Builder\NodeBuilder $nodeBuilder
     */
    public function addConfiguration(NodeBuilder $nodeBuilder)
    {
        $this->addTemplateResolverNode($nodeBuilder, 'block_view');
        $this->addTemplateResolverNode($nodeBuilder, 'layout_view');
    }

    /**
     * Adds semantic configuration for template resolvers
     *
     * @param \Symfony\Component\Config\Definition\Builder\NodeBuilder $nodeBuilder
     * @param string $nodeName
     */
    protected function addTemplateResolverNode(NodeBuilder $nodeBuilder, $nodeName)
    {
        $this->availableParameters[] = $nodeName;

        $nodeBuilder
            ->arrayNode($nodeName)
                ->requiresAtLeastOneElement()
                ->useAttributeAsKey('name')
                ->prototype('array')
                    ->useAttributeAsKey('name')
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
                ->end()
            ->end();
    }

    /**
     * Adds the pagelayout semantic config for the bundle
     *
     * @param \Symfony\Component\Config\Definition\Builder\NodeBuilder $nodeBuilder
     */
    protected function addPageLayoutNode(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->scalarNode('pagelayout')
                ->defaultValue('NetgenBlockManagerBundle::pagelayout_empty.html.twig')
                ->cannotBeEmpty()
            ->end();
    }

    /**
     * Returns parameters which are allowed to be used by other bundles when
     * building their semantic configuration. Useful for eZ Publish Block Manager
     * bundle, which uses this semantic config as a base for config resolver based
     * semantic config
     *
     * @return array
     */
    public function getAvailableParameters()
    {
        return $this->availableParameters;
    }
}
