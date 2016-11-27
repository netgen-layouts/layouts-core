<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection;

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
