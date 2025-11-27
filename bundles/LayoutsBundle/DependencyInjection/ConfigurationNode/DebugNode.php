<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNodeInterface;
use Symfony\Component\Config\Definition\Builder\BooleanNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

final class DebugNode implements ConfigurationNodeInterface
{
    /**
     * @return \Symfony\Component\Config\Definition\Builder\BooleanNodeDefinition<\Symfony\Component\Config\Definition\Builder\NodeParentInterface>
     */
    public function getConfigurationNode(): BooleanNodeDefinition
    {
        $treeBuilder = new TreeBuilder('debug', 'boolean');
        $node = $treeBuilder->getRootNode();

        $node
            ->defaultValue(false);

        return $node;
    }
}
