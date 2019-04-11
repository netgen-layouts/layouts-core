<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNodeInterface;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

final class PageLayoutNode implements ConfigurationNodeInterface
{
    public function getConfigurationNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('pagelayout', 'scalar');
        $node = $treeBuilder->getRootNode();

        $node
            ->defaultValue('@NetgenLayouts/empty_pagelayout.html.twig')
            ->cannotBeEmpty();

        return $node;
    }
}
