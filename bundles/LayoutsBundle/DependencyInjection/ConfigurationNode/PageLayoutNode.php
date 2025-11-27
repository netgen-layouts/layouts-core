<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNodeInterface;
use Symfony\Component\Config\Definition\Builder\StringNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

final class PageLayoutNode implements ConfigurationNodeInterface
{
    /**
     * @return \Symfony\Component\Config\Definition\Builder\StringNodeDefinition<\Symfony\Component\Config\Definition\Builder\NodeParentInterface>
     */
    public function getConfigurationNode(): StringNodeDefinition
    {
        $treeBuilder = new TreeBuilder('pagelayout', 'string');
        $node = $treeBuilder->getRootNode();

        $node
            ->defaultValue('')
            ->cannotBeEmpty();

        return $node;
    }
}
