<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection;

use Jean85\PrettyVersions;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\Handler\PluginInterface;
use Netgen\BlockManager\Collection\Item\VisibilityVoterInterface;
use Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface;
use Netgen\BlockManager\Context\ContextProviderInterface;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\MapperInterface as ConditionTypeFormMapperInterface;
use Netgen\BlockManager\Layout\Resolver\Form\TargetType\MapperInterface as TargetTypeFormMapperInterface;
use Netgen\BlockManager\Layout\Resolver\TargetTypeInterface;
use Netgen\BlockManager\Parameters\Form\MapperInterface as ParameterTypeFormMapperInterface;
use Netgen\BlockManager\Parameters\ParameterTypeInterface;
use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\TargetHandlerInterface;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;
use Netgen\BlockManager\View\Provider\ViewProviderInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\GlobFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

final class NetgenBlockManagerExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ExtensionPluginInterface[]
     */
    private $plugins = [];

    /**
     * Adds a plugin to the extension.
     */
    public function addPlugin(ExtensionPluginInterface $plugin): void
    {
        $this->plugins[get_class($plugin)] = $plugin;
    }

    /**
     * Returns if the plugin exists. Name of the plugin is its fully qualified class name.
     */
    public function hasPlugin(string $pluginName): bool
    {
        return isset($this->plugins[$pluginName]);
    }

    /**
     * Returns the plugin by name. Name of the plugin is its fully qualified class name.
     *
     * @throws \Netgen\BlockManager\Exception\RuntimeException If the specified plugin does not exist
     */
    public function getPlugin(string $pluginName): ExtensionPluginInterface
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
    public function getPlugins(): array
    {
        return $this->plugins;
    }

    public function load(array $configs, ContainerBuilder $container): void
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

        $this->validateCurrentDesign($config['design'], array_keys($config['design_list']));

        $this->loadConfigFiles($container);

        foreach ($config as $key => $value) {
            if ($key !== 'system') {
                $container->setParameter($extensionAlias . '.' . $key, $value);
            }
        }

        $this->registerAutoConfiguration($container);

        $container->setParameter('netgen_block_manager.edition', 'Open Source Edition');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $container->setParameter(
            'ngbm.asset.version',
            PrettyVersions::getVersion('netgen/layouts-core')->getShortCommitHash()
        );

        $prependConfigs = [
            'framework/assets.yml' => 'framework',
            'framework/framework.yml' => 'framework',
            'framework/twig.yml' => 'twig',
            'framework/security.yml' => 'security',
            'design.yml' => 'netgen_block_manager',
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

        /** @var string $configFile */
        foreach (array_reverse($prependConfigs) as $configFile => $prependConfig) {
            if ($configFile[0] !== '/') {
                $configFile = __DIR__ . '/../Resources/config/' . $configFile;
            }

            $config = Yaml::parse((string) file_get_contents($configFile));
            $container->prependExtensionConfig($prependConfig, $config);
            $container->addResource(new FileResource($configFile));
        }
    }

    /**
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface
     */
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration($this);
    }

    /**
     * Loads configuration from various YAML files.
     */
    private function loadConfigFiles(ContainerBuilder $container): void
    {
        $locator = new FileLocator(__DIR__ . '/../Resources/config');

        $loader = new DelegatingLoader(
            new LoaderResolver(
                [
                    new GlobFileLoader($container, $locator),
                    new YamlFileLoader($container, $locator),
                ]
            )
        );

        $loader->load('default_settings.yml');
        $loader->load('browser/services.yml');
        $loader->load('services/**/*.yml', 'glob');
    }

    /**
     * Validates that the design specified in configuration exists in the system.
     *
     * @throws \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException If design does not exist
     */
    private function validateCurrentDesign(string $currentDesign, array $designList): void
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

    private function registerAutoConfiguration(ContainerBuilder $container): void
    {
        $container
            ->registerForAutoconfiguration(ContextProviderInterface::class)
            ->addTag('netgen_block_manager.context.provider');

        $container
            ->registerForAutoconfiguration(ParameterTypeInterface::class)
            ->addTag('netgen_block_manager.parameters.parameter_type');

        $container
            ->registerForAutoconfiguration(ParameterTypeFormMapperInterface::class)
            ->addTag('netgen_block_manager.parameters.form.mapper');

        $container
            ->registerForAutoconfiguration(TargetTypeInterface::class)
            ->addTag('netgen_block_manager.layout.resolver.target_type');

        $container
            ->registerForAutoconfiguration(ConditionTypeInterface::class)
            ->addTag('netgen_block_manager.layout.resolver.condition_type');

        $container
            ->registerForAutoconfiguration(PluginInterface::class)
            ->addTag('netgen_block_manager.block.block_definition_handler.plugin');

        $container
            ->registerForAutoconfiguration(BlockDefinitionHandlerInterface::class)
            ->addTag('netgen_block_manager.block.block_definition_handler');

        $container
            ->registerForAutoconfiguration(QueryTypeHandlerInterface::class)
            ->addTag('netgen_block_manager.collection.query_type_handler');

        $container
            ->registerForAutoconfiguration(TargetTypeFormMapperInterface::class)
            ->addTag('netgen_block_manager.layout.resolver.form.target_type.mapper');

        $container
            ->registerForAutoconfiguration(TargetHandlerInterface::class)
            ->addTag('netgen_block_manager.layout.resolver.target_handler.doctrine');

        $container
            ->registerForAutoconfiguration(ConditionTypeFormMapperInterface::class)
            ->addTag('netgen_block_manager.layout.resolver.form.condition_type.mapper');

        $container
            ->registerForAutoconfiguration(VisibilityVoterInterface::class)
            ->addTag('netgen_block_manager.collection.item_visibility_resolver.voter');

        $container
            ->registerForAutoconfiguration(ViewProviderInterface::class)
            ->addTag('netgen_block_manager.view.provider');

        $container
            ->registerForAutoconfiguration(VisitorInterface::class)
            ->addTag('netgen_block_manager.transfer.serializer.visitor');
    }
}
