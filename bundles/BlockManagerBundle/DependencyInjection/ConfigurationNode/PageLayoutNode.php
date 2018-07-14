<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNodeInterface;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

final class PageLayoutNode implements ConfigurationNodeInterface
{
    public function getConfigurationNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('pagelayout', 'scalar');
        $node = $treeBuilder->getRootNode();

        $node
            ->defaultValue('@NetgenBlockManager/empty_pagelayout.html.twig')
            ->cannotBeEmpty();

        return $node;
    }
}
