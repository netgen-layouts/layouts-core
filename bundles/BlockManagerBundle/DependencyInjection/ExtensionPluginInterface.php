<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

interface ExtensionPluginInterface
{
    /**
     * Pre-processes the configuration before it is resolved.
     *
     * @param array $configs
     *
     * @return array
     */
    public function preProcessConfiguration(array $configs);

    /**
     * Processes the configuration for the bundle.
     *
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $rootNode
     */
    public function addConfiguration(ArrayNodeDefinition $rootNode);

    /**
     * Returns available configuration nodes for the bundle.
     *
     * @return \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNodeInterface[]
     */
    public function getConfigurationNodes();

    /**
     * Post-processes the resolved configuration.
     *
     * @param array $config
     *
     * @return array
     */
    public function postProcessConfiguration(array $config);

    /**
     * Returns the array of files to be appended to main bundle configuration.
     *
     * @return array
     */
    public function appendConfigurationFiles();
}
