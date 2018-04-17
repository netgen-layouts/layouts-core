<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNodeInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

final class DebugNode implements ConfigurationNodeInterface
{
    public function getConfigurationNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('debug', 'boolean');

        $node
            ->defaultValue(false);

        return $node;
    }
}
