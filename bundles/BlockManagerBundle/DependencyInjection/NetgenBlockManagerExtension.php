<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection;

use Netgen\BlockManager\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Yaml\Yaml;

class NetgenBlockManagerExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ExtensionPluginInterface[]
     */
    protected $plugins = array();

    /**
     * Adds a plugin to the extension.
     *
     * @param \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ExtensionPluginInterface $plugin
     */
    public function addPlugin(ExtensionPluginInterface $plugin)
    {
        $this->plugins[get_class($plugin)] = $plugin;
    }

    /**
     * Returns if the plugin exists. Name of the plugin is its fully qualified class name.
     *
     * @param string $pluginName
     *
     * @return bool
     */
    public function hasPlugin($pluginName)
    {
        return isset($this->plugins[$pluginName]);
    }

    /**
     * Returns the plugin by name. Name of the plugin is its fully qualified class name.
     *
     * @param string $pluginName
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the specified plugin does not exist
     *
     * @return \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ExtensionPluginInterface
     */
    public function getPlugin($pluginName)
    {
        if (!isset($this->plugins[$pluginName])) {
            throw new InvalidArgumentException(
                'name',
                sprintf(
                    'Extension plugin "%s" does not exist',
                    $pluginName
                )
            );
        }

        return $this->plugins[$pluginName];
    }

    /**
     * Returns the all available plugins.
     *
     * @return \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ExtensionPluginInterface[]
     */
    public function getPlugins()
    {
        return $this->plugins;
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

        foreach ($this->plugins as $plugin) {
            $configs = $plugin->preProcessConfiguration($configs);
        }

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($this->plugins as $plugin) {
            $config = $plugin->postProcessConfiguration($config);
        }

        $this->loadConfigFiles($container);

        foreach ($config as $key => $value) {
            if ($key !== 'system') {
                $container->setParameter($extensionAlias . '.' . $key, $value);
            }
        }
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
            'view/block_view.yml' => 'netgen_block_manager',
            'view/layout_view.yml' => 'netgen_block_manager',
            'view/parameter_view.yml' => 'netgen_block_manager',
            'view/default_templates.yml' => 'netgen_block_manager',
            'browser/item_types.yml' => 'netgen_content_browser',
        );

        foreach ($this->plugins as $plugin) {
            foreach ($plugin->appendConfigurationFiles() as $configFile) {
                $prependConfigs[$configFile] = 'netgen_block_manager';
            }
        }

        foreach (array_reverse($prependConfigs) as $configFile => $prependConfig) {
            if ($configFile[0] !== '/') {
                $configFile = __DIR__ . '/../Resources/config/' . $configFile;
            }

            $config = Yaml::parse(file_get_contents($configFile));
            $container->prependExtensionConfig($prependConfig, $config);
            $container->addResource(new FileResource($configFile));
        }
    }

    /**
     * Returns extension configuration.
     *
     * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface
     */
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration($this);
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
        $loader->load('services/forms.yml');

        $loader->load('services/layout_resolver/layout_resolver.yml');
        $loader->load('services/layout_resolver/condition_types.yml');
        $loader->load('services/layout_resolver/target_handlers.yml');
        $loader->load('services/layout_resolver/target_types.yml');
        $loader->load('services/layout_resolver/forms.yml');

        $loader->load('services/collection/collections.yml');
        $loader->load('services/collection/query_types.yml');

        $loader->load('browser/services.yml');

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
}
