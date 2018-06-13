<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection;

use Jean85\PrettyVersions;
use Netgen\BlockManager\Exception\RuntimeException;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Yaml\Yaml;

final class NetgenBlockManagerExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ExtensionPluginInterface[]
     */
    private $plugins = [];

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
     * @throws \Netgen\BlockManager\Exception\RuntimeException If the specified plugin does not exist
     *
     * @return \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ExtensionPluginInterface
     */
    public function getPlugin($pluginName)
    {
        if (!isset($this->plugins[$pluginName])) {
            throw new RuntimeException(
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

        $this->processHttpCacheConfiguration($config['http_cache'], $container);
        $this->validateCurrentDesign($config['design'], array_keys($config['design_list']));

        $this->loadConfigFiles($container);

        foreach ($config as $key => $value) {
            if ($key !== 'system') {
                $container->setParameter($extensionAlias . '.' . $key, $value);
            }
        }
    }

    public function prepend(ContainerBuilder $container)
    {
        $container->setParameter(
            'ngbm.asset.version',
            PrettyVersions::getVersion('netgen/block-manager')->getShortCommitHash()
        );

        $prependConfigs = [
            'framework/assets.yml' => 'framework',
            'framework/framework.yml' => 'framework',
            'framework/twig.yml' => 'twig',
            'framework/security.yml' => 'security',
            'design.yml' => 'netgen_block_manager',
            'http_cache.yml' => 'netgen_block_manager',
            'block_type_groups.yml' => 'netgen_block_manager',
            'view/block_view.yml' => 'netgen_block_manager',
            'view/layout_view.yml' => 'netgen_block_manager',
            'view/item_view.yml' => 'netgen_block_manager',
            'view/parameter_view.yml' => 'netgen_block_manager',
            'view/default_templates.yml' => 'netgen_block_manager',
            'browser/item_types.yml' => 'netgen_content_browser',
        ];

        foreach ($this->plugins as $plugin) {
            foreach ($plugin->appendConfigurationFiles() as $configFile) {
                $prependConfigs[$configFile] = 'netgen_block_manager';
            }
        }

        foreach (array_reverse($prependConfigs) as $configFile => $prependConfig) {
            if ($configFile[0] !== '/') {
                $configFile = __DIR__ . '/../Resources/config/' . $configFile;
            }

            $config = Yaml::parse((string) file_get_contents($configFile));
            $container->prependExtensionConfig($prependConfig, $config);
            $container->addResource(new FileResource($configFile));
        }
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration($this);
    }

    /**
     * Loads configuration from various YAML files.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    private function loadConfigFiles(ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('default_settings.yml');
        $loader->load('services/errors.yml');
        $loader->load('services/view/providers.yml');
        $loader->load('services/view/matchers.yml');
        $loader->load('services/view/view.yml');

        $loader->load('services/items.yml');
        $loader->load('services/block_definitions.yml');
        $loader->load('services/config_definitions.yml');
        $loader->load('services/forms.yml');
        $loader->load('services/context.yml');
        $loader->load('services/commands.yml');
        $loader->load('services/design.yml');

        $loader->load('services/layout_resolver/layout_resolver.yml');
        $loader->load('services/layout_resolver/condition_types.yml');
        $loader->load('services/layout_resolver/target_handlers.yml');
        $loader->load('services/layout_resolver/target_types.yml');
        $loader->load('services/layout_resolver/forms.yml');

        $loader->load('browser/services.yml');
        $loader->load('services/layouts.yml');
        $loader->load('services/collections.yml');
        $loader->load('services/param_converters.yml');
        $loader->load('services/event_listeners.yml');

        if (Kernel::VERSION_ID >= 30400 && Kernel::VERSION_ID < 40100) {
            $loader->load('services/event_listeners_sf34.yml');
        }

        $loader->load('services/configuration.yml');
        $loader->load('services/controllers.yml');
        $loader->load('services/normalizers.yml');
        $loader->load('services/validators.yml');
        $loader->load('services/templating.yml');
        $loader->load('services/parameters.yml');
        $loader->load('services/http_cache.yml');
        $loader->load('services/locale.yml');
        $loader->load('services/commands.yml');

        $loader->load('services/transfer/serialization_visitors.yml');
        $loader->load('services/transfer/services.yml');

        $loader->load('services/api.yml');
    }

    /**
     * Processes configuration for HTTP cache.
     *
     * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    private function processHttpCacheConfiguration(array $config, ContainerBuilder $container)
    {
        $container->setParameter(
            'netgen_block_manager.http_cache.ttl.default.block',
            $config['ttl']['default']['block']
        );

        $container->setParameter(
            'netgen_block_manager.http_cache.ttl.block_definition',
            $config['ttl']['block_definition']
        );
    }

    /**
     * Validates that the design specified in configuration exists in the system.
     *
     * @param string $currentDesign
     * @param array $designList
     *
     * @throws \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException If design does not exist
     */
    private function validateCurrentDesign($currentDesign, array $designList)
    {
        if ($currentDesign !== 'standard' && !in_array($currentDesign, $designList, true)) {
            throw new InvalidConfigurationException(
                sprintf(
                    'Design "%s" does not exist. Available designs are: %s',
                    $currentDesign,
                    implode(', ', $designList)
                )
            );
        }
    }
}
