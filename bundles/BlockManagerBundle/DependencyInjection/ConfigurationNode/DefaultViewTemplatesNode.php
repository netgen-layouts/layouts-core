<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNodeInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

final class DefaultViewTemplatesNode implements ConfigurationNodeInterface
{
    public function getConfigurationNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('default_view_templates');

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('view')
            ->prototype('array')
                ->requiresAtLeastOneElement()
                ->useAttributeAsKey('context')
                ->prototype('scalar')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
            ->end();

        return $node;
    }
}
