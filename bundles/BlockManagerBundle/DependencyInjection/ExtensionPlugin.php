<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

abstract class ExtensionPlugin implements ExtensionPluginInterface
{
    public function preProcessConfiguration(array $configs)
    {
        return $configs;
    }

    public function addConfiguration(ArrayNodeDefinition $rootNode)
    {
        $children = $rootNode->children();

        foreach ($this->getConfigurationNodes() as $node) {
            $children->append($node->getConfigurationNode());
        }
    }

    public function getConfigurationNodes()
    {
        return [];
    }

    public function postProcessConfiguration(array $config)
    {
        return $config;
    }

    public function appendConfigurationFiles()
    {
        return [];
    }
}
