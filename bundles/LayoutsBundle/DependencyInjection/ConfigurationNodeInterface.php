<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;

interface ConfigurationNodeInterface
{
    /**
     * Returns a node definition.
     *
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition<\Symfony\Component\Config\Definition\Builder\TreeBuilder>
     */
    public function getConfigurationNode(): NodeDefinition;
}
