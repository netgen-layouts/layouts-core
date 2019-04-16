<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNodeInterface;
use Netgen\Layouts\Utils\BackwardsCompatibility\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

final class DefaultViewTemplatesNode implements ConfigurationNodeInterface
{
    public function getConfigurationNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('default_view_templates');
        $node = $treeBuilder->getRootNode();

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('view')
            ->arrayPrototype()
                ->requiresAtLeastOneElement()
                ->useAttributeAsKey('context')
                ->scalarPrototype()
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
            ->end();

        return $node;
    }
}
