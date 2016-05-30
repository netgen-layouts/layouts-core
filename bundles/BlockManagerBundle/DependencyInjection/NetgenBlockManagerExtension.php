<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\DefinitionDecorator;
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
     * Adds the config tree builder closure.
     *
     * @param \Closure $configTreeBuilder
     */
    public function addConfigTreeBuilder(Closure $configTreeBuilder)
    {
        $this->configTreeBuilders[] = $configTreeBuilder;
    }

    /**
     * Adds the config preprocessor closure.
     *
     * @param \Closure $preProcessor
     */
    public function addPreProcessor(Closure $preProcessor)
    {
        $this->preProcessors[] = $preProcessor;
    }

    /**
     * Adds the config post processor closure.
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
            if ($key !== 'system') {
                $container->setParameter($extensionAlias . '.' . $key, $value);
            }
        }

        $this->buildConfigObjects($container, 'layout_type', $config['layout_types']);
        $this->buildConfigObjects($container, 'source', $config['sources']);
        $this->buildConfigObjects($container, 'block_type', $config['block_types']);
        $this->buildConfigObjects($container, 'block_type_group', $config['block_type_groups']);
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $prependConfigs = array(
            'framework/framework.yml' => 'framework',
            'block_definitions.yml' => 'netgen_block_manager',
            'block_type_groups.yml' => 'netgen_block_manager',
            'block_types.yml' => 'netgen_block_manager',
            'layout_types.yml' => 'netgen_block_manager',
            'sources.yml' => 'netgen_block_manager',
            'query_types.yml' => 'netgen_block_manager',
            'view/block_view.yml' => 'netgen_block_manager',
            'view/item_view.yml' => 'netgen_block_manager',
            'view/layout_view.yml' => 'netgen_block_manager',
            'view/query_view.yml' => 'netgen_block_manager',
        );

        foreach ($prependConfigs as $configFile => $prependConfig) {
            $configFile = __DIR__ . '/../Resources/config/' . $configFile;
            $config = Yaml::parse(file_get_contents($configFile));
            $container->prependExtensionConfig($prependConfig, $config);
            $container->addResource(new FileResource($configFile));
        }
    }

    /**
     * Loads configuration from various YAML files.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function loadConfigFiles(ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('default_settings.yml');
        $loader->load('services/view/providers.yml');
        $loader->load('services/view/matchers.yml');
        $loader->load('services/view/view.yml');

        $loader->load('services/items.yml');
        $loader->load('services/block_definitions.yml');
        $loader->load('services/block_forms.yml');
        $loader->load('services/query_forms.yml');

        $loader->load('services/layout_resolver/layout_resolver.yml');
        $loader->load('services/layout_resolver/condition_matchers.yml');
        $loader->load('services/layout_resolver/target_handlers.yml');
        $loader->load('services/layout_resolver/target_value_providers.yml');

        $loader->load('services/collection/collections.yml');
        $loader->load('services/collection/query_types.yml');

        $loader->load('services/param_converters.yml');
        $loader->load('services/event_listeners.yml');
        $loader->load('services/configuration.yml');
        $loader->load('services/controllers.yml');
        $loader->load('services/normalizers.yml');
        $loader->load('services/validators.yml');
        $loader->load('services/templating.yml');
        $loader->load('services/parameters.yml');

        $loader->load('services/api.yml');
    }

    /**
     * Builds the config objects from provided array config.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param string $configName
     * @param array $configs
     */
    protected function buildConfigObjects(ContainerBuilder $container, $configName, array $configs = array())
    {
        foreach ($configs as $identifier => $config) {
            $serviceIdentifier = sprintf('netgen_block_manager.configuration.%s.%s', $configName, $identifier);

            $container
                ->setDefinition(
                    $serviceIdentifier,
                    new DefinitionDecorator(sprintf('netgen_block_manager.configuration.%s', $configName))
                )
                ->setArguments(array($config, $identifier))
                ->addTag(sprintf('netgen_block_manager.configuration.%s', $configName), array('identifier' => $identifier))
                ->setAbstract(false);
        }
    }
}
