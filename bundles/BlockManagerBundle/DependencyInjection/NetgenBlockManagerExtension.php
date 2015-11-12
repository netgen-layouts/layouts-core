<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Yaml\Yaml;

class NetgenBlockManagerExtension extends Extension implements PrependExtensionInterface
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
        $extensionAlias = $this->getAlias();
        $configuration = new Configuration($extensionAlias);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('view/template_resolvers.yml');
        $loader->load('view/providers.yml');
        $loader->load('view/matchers.yml');

        $loader->load('block_definitions.yml');
        $loader->load('block_groups.yml');
        $loader->load('layouts.yml');
        $loader->load('blocks.yml');

        $loader->load('param_converters.yml');
        $loader->load('event_listeners.yml');
        $loader->load('controllers.yml');
        $loader->load('normalizers.yml');
        $loader->load('templating.yml');
        $loader->load('services.yml');

        $loader->load('api.yml');

        foreach ($config as $key => $value) {
            $container->setParameter($extensionAlias . '.' . $key, $value);
        }

        $container->setParameter(
            $extensionAlias . '.available_parameters',
            $configuration->getAvailableParameters()
        );
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $config = array(
            'serializer' => array(
                'enabled' => true,
            ),
        );

        $container->prependExtensionConfig('framework', $config);

        $extensionAlias = $this->getAlias();

        $configFile = __DIR__ . '/../Resources/config/view/block_view.yml';
        $config = Yaml::parse(file_get_contents($configFile));
        $container->prependExtensionConfig($extensionAlias, $config);
        $container->addResource(new FileResource($configFile));

        $configFile = __DIR__ . '/../Resources/config/view/layout_view.yml';
        $config = Yaml::parse(file_get_contents($configFile));
        $container->prependExtensionConfig($extensionAlias, $config);
        $container->addResource(new FileResource($configFile));
    }
}
