<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class NetgenBlockManagerExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array $configs
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('view/template_providers.yml');
        $loader->load('view/renderers.yml');
        $loader->load('view/builders.yml');

        $loader->load('block_definitions.yml');
        $loader->load('block_groups.yml');
        $loader->load('layouts.yml');
        $loader->load('blocks.yml');

        $loader->load('param_converters.yml');
        $loader->load('controllers.yml');
        $loader->load('serializers.yml');
        $loader->load('registries.yml');

        $loader->load('api.yml');
    }
}
