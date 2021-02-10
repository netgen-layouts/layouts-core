<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection;

use Netgen\Layouts\Utils\BackwardsCompatibility\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder as BaseTreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    private NetgenLayoutsExtension $extension;

    public function __construct(NetgenLayoutsExtension $extension)
    {
        $this->extension = $extension;
    }

    public function getConfigTreeBuilder(): BaseTreeBuilder
    {
        $treeBuilder = new TreeBuilder($this->extension->getAlias());

        $rootNode = $treeBuilder->getRootNode();
        $children = $rootNode->children();

        foreach ($this->getNodes() as $node) {
            $children->append($node->getConfigurationNode());
        }

        foreach ($this->extension->getPlugins() as $plugin) {
            if ($rootNode instanceof ArrayNodeDefinition) {
                $plugin->addConfiguration($rootNode);
            }
        }

        $children->end();

        return $treeBuilder;
    }

    /**
     * Returns available configuration nodes for the bundle.
     *
     * @return \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNodeInterface[]
     */
    private function getNodes(): array
    {
        return [
            new ConfigurationNode\ViewNode(),
            new ConfigurationNode\DesignNode(),
            new ConfigurationNode\DesignListNode(),
            new ConfigurationNode\DefaultViewTemplatesNode(),
            new ConfigurationNode\HttpCacheNode(),
            new ConfigurationNode\BlockDefinitionNode(),
            new ConfigurationNode\BlockTypeNode(),
            new ConfigurationNode\BlockTypeGroupNode(),
            new ConfigurationNode\LayoutTypeNode(),
            new ConfigurationNode\QueryTypeNode(),
            new ConfigurationNode\PageLayoutNode(),
            new ConfigurationNode\ApiKeysNode(),
            new ConfigurationNode\ValueTypeNode(),
            new ConfigurationNode\DebugNode(),
        ];
    }
}
