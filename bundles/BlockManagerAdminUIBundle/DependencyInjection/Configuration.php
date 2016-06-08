<?php

namespace Netgen\Bundle\BlockManagerAdminUIBundle\DependencyInjection;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration as BlockManagerConfiguration;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
    }

    /**
     * Returns the config tree builder closure.
     *
     * @return \Closure
     */
    public function getConfigTreeBuilderClosure()
    {
        return function (ArrayNodeDefinition $rootNode, BlockManagerConfiguration $configuration) {
        };
    }
}
