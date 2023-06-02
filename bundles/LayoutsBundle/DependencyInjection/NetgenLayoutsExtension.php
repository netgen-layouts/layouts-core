<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection;

use Jean85\PrettyVersions;
use Netgen\Layouts\Attribute;
use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\Layouts\Block\BlockDefinition\Handler\PluginInterface;
use Netgen\Layouts\Collection\Item\VisibilityVoterInterface;
use Netgen\Layouts\Collection\QueryType\QueryTypeHandlerInterface;
use Netgen\Layouts\Context\ContextProviderInterface;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Item\ValueConverterInterface;
use Netgen\Layouts\Item\ValueLoaderInterface;
use Netgen\Layouts\Item\ValueUrlGeneratorInterface;
use Netgen\Layouts\Layout\Resolver\ConditionTypeInterface;
use Netgen\Layouts\Layout\Resolver\Form\ConditionType\MapperInterface as ConditionTypeFormMapperInterface;
use Netgen\Layouts\Layout\Resolver\Form\TargetType\MapperInterface as TargetTypeFormMapperInterface;
use Netgen\Layouts\Layout\Resolver\TargetTypeInterface;
use Netgen\Layouts\Parameters\Form\MapperInterface as ParameterTypeFormMapperInterface;
use Netgen\Layouts\Parameters\ParameterTypeInterface;
use Netgen\Layouts\Persistence\Doctrine\QueryHandler\TargetHandlerInterface;
use Netgen\Layouts\Transfer\Output\VisitorInterface;
use Netgen\Layouts\Utils\BackwardsCompatibility\YamlFileLoader;
use Netgen\Layouts\View\Matcher\MatcherInterface;
use Netgen\Layouts\View\Provider\ViewProviderInterface;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\GlobFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

use function array_keys;
use function array_reverse;
use function file_exists;
use function file_get_contents;
use function get_class;
use function implode;
use function in_array;
use function method_exists;
use function sprintf;

use const PHP_VERSION_ID;

final class NetgenLayoutsExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @var array<class-string<\Netgen\Bundle\LayoutsBundle\DependencyInjection\ExtensionPluginInterface>, \Netgen\Bundle\LayoutsBundle\DependencyInjection\ExtensionPluginInterface>
     */
    private array $plugins = [];

    /**
     * Adds a plugin to the extension.
     */
    public function addPlugin(ExtensionPluginInterface $plugin): void
    {
        $this->plugins[get_class($plugin)] = $plugin;
    }

    /**
     * Returns if the plugin exists. Name of the plugin is its fully qualified class name.
     *
     * @param class-string<\Netgen\Bundle\LayoutsBundle\DependencyInjection\ExtensionPluginInterface> $pluginName
     */
    public function hasPlugin(string $pluginName): bool
    {
        return isset($this->plugins[$pluginName]);
    }

    /**
     * Returns the plugin by name. Name of the plugin is its fully qualified class name.
     *
     * @param class-string<\Netgen\Bundle\LayoutsBundle\DependencyInjection\ExtensionPluginInterface> $pluginName
     *
     * @throws \Netgen\Layouts\Exception\RuntimeException If the specified plugin does not exist
     */
    public function getPlugin(string $pluginName): ExtensionPluginInterface
    {
        if (!isset($this->plugins[$pluginName])) {
            throw new RuntimeException(
                sprintf(
                    'Extension plugin "%s" does not exist',
                    $pluginName,
                ),
            );
        }

        return $this->plugins[$pluginName];
    }

    /**
     * Returns the all available plugins.
     *
     * @return array<class-string<\Netgen\Bundle\LayoutsBundle\DependencyInjection\ExtensionPluginInterface>, \Netgen\Bundle\LayoutsBundle\DependencyInjection\ExtensionPluginInterface>
     */
    public function getPlugins(): array
    {
        return $this->plugins;
    }

    /**
     * @param mixed[] $configs
     */
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

        /** @var string[] $designList */
        $designList = array_keys($config['design_list']);
        $this->validateCurrentDesign($config['design'], $designList);

        $this->loadConfigFiles($container);

        foreach ($config as $key => $value) {
            if ($key !== 'system') {
                $container->setParameter($extensionAlias . '.' . $key, $value);
            }
        }

        $this->registerAutoConfiguration($container);

        if (PHP_VERSION_ID >= 80000 && method_exists($container, 'registerAttributeForAutoconfiguration')) {
            $this->registerAttributeAutoConfiguration($container);
        }

        $container->setParameter('netgen_layouts.edition', 'Open Source Edition');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $container->setParameter(
            'netgen_layouts.asset_version',
            PrettyVersions::getVersion('netgen/layouts-core')->getShortReference(),
        );

        $prependConfigs = [
            'framework/assets.yaml' => 'framework',
            'framework/framework.yaml' => 'framework',
            'framework/twig.yaml' => 'twig',
            'framework/security.yaml' => 'security',
            'design.yaml' => 'netgen_layouts',
            'block_type_groups.yaml' => 'netgen_layouts',
            'view/block_view.yaml' => 'netgen_layouts',
            'view/layout_view.yaml' => 'netgen_layouts',
            'view/item_view.yaml' => 'netgen_layouts',
            'view/parameter_view.yaml' => 'netgen_layouts',
            'view/default_templates.yaml' => 'netgen_layouts',
            'browser/item_types.yaml' => 'netgen_content_browser',
        ];

        foreach ($this->plugins as $plugin) {
            foreach ($plugin->appendConfigurationFiles() as $configFile) {
                $prependConfigs[$configFile] = 'netgen_layouts';
            }
        }

        /** @var string $configFile */
        foreach (array_reverse($prependConfigs) as $configFile => $prependConfig) {
            if (!file_exists($configFile)) {
                $configFile = __DIR__ . '/../Resources/config/' . $configFile;
            }

            $config = Yaml::parse((string) file_get_contents($configFile));
            $container->prependExtensionConfig($prependConfig, $config);
            $container->addResource(new FileResource($configFile));
        }
    }

    /**
     * @param mixed[] $config
     */
    public function getConfiguration(array $config, ContainerBuilder $container): ConfigurationInterface
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
                ],
            ),
        );

        $loader->load('default_settings.yaml');
        $loader->load('browser/services.yaml');
        $loader->load('services/**/*.yaml', 'glob');
    }

    /**
     * Validates that the design specified in configuration exists in the system.
     *
     * @param string[] $designList
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
                    implode(', ', $designList),
                ),
            );
        }
    }

    private function registerAutoConfiguration(ContainerBuilder $container): void
    {
        $container
            ->registerForAutoconfiguration(ContextProviderInterface::class)
            ->addTag('netgen_layouts.context_provider');

        $container
            ->registerForAutoconfiguration(ParameterTypeInterface::class)
            ->addTag('netgen_layouts.parameter_type');

        $container
            ->registerForAutoconfiguration(ParameterTypeFormMapperInterface::class)
            ->addTag('netgen_layouts.parameter_type.form_mapper');

        $container
            ->registerForAutoconfiguration(TargetTypeInterface::class)
            ->addTag('netgen_layouts.target_type');

        $container
            ->registerForAutoconfiguration(TargetTypeFormMapperInterface::class)
            ->addTag('netgen_layouts.target_type.form_mapper');

        $container
            ->registerForAutoconfiguration(TargetHandlerInterface::class)
            ->addTag('netgen_layouts.target_type.doctrine_handler');

        $container
            ->registerForAutoconfiguration(ConditionTypeInterface::class)
            ->addTag('netgen_layouts.condition_type');

        $container
            ->registerForAutoconfiguration(ConditionTypeFormMapperInterface::class)
            ->addTag('netgen_layouts.condition_type.form_mapper');

        $container
            ->registerForAutoconfiguration(BlockDefinitionHandlerInterface::class)
            ->addTag('netgen_layouts.block_definition_handler');

        $container
            ->registerForAutoconfiguration(PluginInterface::class)
            ->addTag('netgen_layouts.block_definition_handler.plugin');

        $container
            ->registerForAutoconfiguration(QueryTypeHandlerInterface::class)
            ->addTag('netgen_layouts.query_type_handler');

        $container
            ->registerForAutoconfiguration(VisibilityVoterInterface::class)
            ->addTag('netgen_layouts.item_visibility_voter');

        $container
            ->registerForAutoconfiguration(ViewProviderInterface::class)
            ->addTag('netgen_layouts.view_provider');

        $container
            ->registerForAutoconfiguration(VisitorInterface::class)
            ->addTag('netgen_layouts.transfer_output_visitor');

        $container
            ->registerForAutoconfiguration(ValueConverterInterface::class)
            ->addTag('netgen_layouts.cms_value_converter');

        $container
            ->registerForAutoconfiguration(ValueLoaderInterface::class)
            ->addTag('netgen_layouts.cms_value_loader');

        $container
            ->registerForAutoconfiguration(ValueUrlGeneratorInterface::class)
            ->addTag('netgen_layouts.cms_value_url_generator');

        $container
            ->registerForAutoconfiguration(MatcherInterface::class)
            ->addTag('netgen_layouts.view_matcher');
    }

    private function registerAttributeAutoConfiguration(ContainerBuilder $container): void
    {
        $container->registerAttributeForAutoconfiguration(
            Attribute\AsBlockDefinitionHandler::class,
            static function (ChildDefinition $definition, Attribute\AsBlockDefinitionHandler $attribute): void {
                $definition->addTag('netgen_layouts.block_definition_handler', ['identifier' => $attribute->identifier]);
            },
        );

        $container->registerAttributeForAutoconfiguration(
            Attribute\AsBlockPlugin::class,
            static function (ChildDefinition $definition, Attribute\AsBlockPlugin $attribute): void {
                $definition->addTag('netgen_layouts.block_definition_handler.plugin', ['priority' => $attribute->priority]);
            },
        );

        $container->registerAttributeForAutoconfiguration(
            Attribute\AsQueryTypeHandler::class,
            static function (ChildDefinition $definition, Attribute\AsQueryTypeHandler $attribute): void {
                $definition->addTag('netgen_layouts.query_type_handler', ['type' => $attribute->type]);
            },
        );

        $container->registerAttributeForAutoconfiguration(
            Attribute\AsParameterType::class,
            static function (ChildDefinition $definition): void {
                $definition->addTag('netgen_layouts.parameter_type');
            },
        );

        $container->registerAttributeForAutoconfiguration(
            Attribute\AsParameterTypeFormMapper::class,
            static function (ChildDefinition $definition, Attribute\AsParameterTypeFormMapper $attribute): void {
                $definition->addTag('netgen_layouts.parameter_type.form_mapper', ['type' => $attribute->type]);
            },
        );

        $container->registerAttributeForAutoconfiguration(
            Attribute\AsCmsValueLoader::class,
            static function (ChildDefinition $definition, Attribute\AsCmsValueLoader $attribute): void {
                $definition->addTag('netgen_layouts.cms_value_loader', ['value_type' => $attribute->valueType]);
            },
        );

        $container->registerAttributeForAutoconfiguration(
            Attribute\AsCmsValueConverter::class,
            static function (ChildDefinition $definition): void {
                $definition->addTag('netgen_layouts.cms_value_converter');
            },
        );

        $container->registerAttributeForAutoconfiguration(
            Attribute\AsCmsValueUrlGenerator::class,
            static function (ChildDefinition $definition, Attribute\AsCmsValueUrlGenerator $attribute): void {
                $definition->addTag('netgen_layouts.cms_value_url_generator', ['value_type' => $attribute->valueType]);
            },
        );
    }
}
