<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('netgen_block_manager');
        $children = $rootNode->children();

        $this->addTemplateResolverNode($children, 'block_view');
        $this->addTemplateResolverNode($children, 'layout_view');

        $children->end();
        return $treeBuilder;
    }

    /**
     * Adds semantic configuration for template resolvers
     *
     * @param \Symfony\Component\Config\Definition\Builder\NodeBuilder $nodeBuilder
     * @param string $nodeName
     */
    public function addTemplateResolverNode(NodeBuilder $nodeBuilder, $nodeName)
    {
        $nodeBuilder
            ->arrayNode($nodeName)
                ->requiresAtLeastOneElement()
                ->prototype('array')
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
}
