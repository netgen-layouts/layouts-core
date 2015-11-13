<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Yaml\Yaml;
use Closure;

class NetgenBlockManagerExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @var \Closure[]
     */
    protected $configTreeBuilders = array();

    /**
     * @var \Closure[]
     */
    protected $preProcessors = array();

    /**
     * @var \Closure[]
     */
    protected $postProcessors = array();

    /**
     * Adds the config tree builder closure
     *
     * @param \Closure $configTreeBuilder
     */
    public function addConfigTreeBuilder(Closure $configTreeBuilder)
    {
        $this->configTreeBuilders[] = $configTreeBuilder;
    }

    /**
     * Adds the config preprocessor closure
     *
     * @param \Closure $preProcessor
     */
    public function addPreProcessor(Closure $preProcessor)
    {
        $this->preProcessors[] = $preProcessor;
    }

    /**
     * Adds the config post processor closure
     *
     * @param \Closure $postProcessor
     */
    public function addPostProcessor(Closure $postProcessor)
    {
        $this->postProcessors[] = $postProcessor;
    }

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

        foreach ($this->preProcessors as $preProcessor) {
            $configs = $preProcessor($configs, $container);
        }

        $configuration = new Configuration($extensionAlias, $this->configTreeBuilders);
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($this->postProcessors as $postProcessor) {
            $config = $postProcessor($config, $container);
        }

        $this->loadConfigFiles($container);

        foreach ($config as $key => $value) {
            $container->setParameter($extensionAlias . '.' . $key, $value);
        }
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

        $prependConfigs = array(
            'blocks.yml',
            'block_groups.yml',
            'view/block_view.yml',
            'view/layout_view.yml'
        );

        foreach ($prependConfigs as $prependConfig) {
            $configFile = __DIR__ . '/../Resources/config/' . $prependConfig;
            $config = Yaml::parse(file_get_contents($configFile));
            $container->prependExtensionConfig($this->getAlias(), $config);
            $container->addResource(new FileResource($configFile));
        }
    }

    /**
     * Loads configuration from various YAML files
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function loadConfigFiles(ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('view/template_resolvers.yml');
        $loader->load('view/providers.yml');
        $loader->load('view/matchers.yml');

        $loader->load('block_definitions.yml');
        $loader->load('layouts.yml');

        $loader->load('param_converters.yml');
        $loader->load('event_listeners.yml');
        $loader->load('controllers.yml');
        $loader->load('normalizers.yml');
        $loader->load('templating.yml');
        $loader->load('services.yml');

        $loader->load('api.yml');
    }
}
