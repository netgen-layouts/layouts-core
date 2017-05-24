<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension
     */
    protected $extension;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension $extension
     */
    public function __construct($extension)
    {
        $this->extension = $extension;
    }

    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root($this->extension->getAlias());
        $children = $rootNode->children();

        foreach ($this->getNodes() as $node) {
            $children->append($node->getConfigurationNode());
        }

        foreach ($this->extension->getPlugins() as $plugin) {
            $plugin->addConfiguration($rootNode);
        }

        $children->end();

        return $treeBuilder;
    }

    /**
     * Returns available configuration nodes for the bundle.
     *
     * @return \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNodeInterface[]
     */
    protected function getNodes()
    {
        return array(
            new ConfigurationNode\ViewNode(),
            new ConfigurationNode\DefaultViewTemplatesNode(),
            new ConfigurationNode\HttpCacheNode(),
            new ConfigurationNode\BlockDefinitionNode(),
            new ConfigurationNode\BlockTypeNode(),
            new ConfigurationNode\BlockTypeGroupNode(),
            new ConfigurationNode\LayoutTypeNode(),
            new ConfigurationNode\QueryTypeNode(),
            new ConfigurationNode\PageLayoutNode(),
            new ConfigurationNode\GoogleMapsNode(),
            new ConfigurationNode\ItemsNode(),
        );
    }
}
