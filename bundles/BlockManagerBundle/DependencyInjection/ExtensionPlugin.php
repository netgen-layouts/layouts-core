<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

abstract class ExtensionPlugin implements ExtensionPluginInterface
{
    /**
     * Pre-processes the configuration before it is resolved.
     *
     * @param array $configs
     *
     * @return array
     */
    public function preProcessConfiguration(array $configs)
    {
        return $configs;
    }

    /**
     * Processes the configuration for the bundle.
     *
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    public function addConfiguration(ArrayNodeDefinition $rootNode)
    {
        $children = $rootNode->children();

        foreach ($this->getConfigurationNodes() as $node) {
            $children->append($node->getConfigurationNode());
        }
    }

    /**
     * Returns available configuration nodes for the bundle.
     *
     * @return \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNodeInterface[]
     */
    public function getConfigurationNodes()
    {
        return array();
    }

    /**
     * Post-processes the resolved configuration.
     *
     * @param array $config
     *
     * @return array
     */
    public function postProcessConfiguration(array $config)
    {
        return $config;
    }

    /**
     * Returns the array of files to be appended to main bundle configuration.
     *
     * @return array
     */
    public function appendConfigurationFiles()
    {
        return array();
    }
}
