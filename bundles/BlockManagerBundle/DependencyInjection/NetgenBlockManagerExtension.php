<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
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
     * @var array
     */
    protected $appendConfigs = array();

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
     * Adds the config files that should be appended to config files of this bundle.
     *
     * @param array $configs
     */
    public function addAppendConfigs(array $configs)
    {
        $this->appendConfigs += $configs;
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

        $configuration = $this->getConfiguration($configs, $container);
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

        $this->buildLayoutTypes($container, $config['layout_types']);
        $this->buildSources($container, $config['sources']);
        $blockTypeConfigs = $this->buildBlockTypes($container, $config['block_definitions'], $config['block_types']);
        $this->buildBlockTypeGroups($container, $config['block_type_groups'], $blockTypeConfigs);
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
        ) + $this->appendConfigs;

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
        return new Configuration($this->getAlias(), $this->configTreeBuilders);
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

    /**
     * Builds the config objects from provided array config.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array $configs
     *
     * @return array
     */
    protected function buildLayoutTypes(ContainerBuilder $container, array $configs = array())
    {
        foreach ($configs as $identifier => $config) {
            if (!$config['enabled']) {
                continue;
            }

            $serviceIdentifier = sprintf('netgen_block_manager.configuration.layout_type.%s', $identifier);

            $container
                ->setDefinition(
                    $serviceIdentifier,
                    new DefinitionDecorator('netgen_block_manager.configuration.layout_type')
                )
                ->setArguments(array($identifier, $config))
                ->addTag('netgen_block_manager.configuration.layout_type')
                ->setAbstract(false);
        }

        return $configs;
    }

    /**
     * Builds the config objects from provided array config.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array $configs
     *
     * @return array
     */
    protected function buildSources(ContainerBuilder $container, array $configs = array())
    {
        foreach ($configs as $identifier => $config) {
            if (!$config['enabled']) {
                continue;
            }

            $serviceIdentifier = sprintf('netgen_block_manager.configuration.source.%s', $identifier);

            $queryTypeReferences = array();
            foreach ($config['queries'] as $queryIdentifier => $queryConfig) {
                $queryTypeReferences[$queryIdentifier] = new Reference(
                    sprintf(
                        'netgen_block_manager.collection.query_type.%s',
                        $queryConfig['query_type']
                    )
                );
            }

            $container
                ->setDefinition(
                    $serviceIdentifier,
                    new DefinitionDecorator('netgen_block_manager.configuration.source')
                )
                ->setArguments(array($identifier, $config, $queryTypeReferences))
                ->addTag('netgen_block_manager.configuration.source')
                ->setAbstract(false);
        }

        return $configs;
    }

    /**
     * Builds the config objects from provided array config.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array $blockDefinitionConfigs
     * @param array $blockTypeConfigs
     *
     * @return array
     */
    protected function buildBlockTypes(
        ContainerBuilder $container,
        array $blockDefinitionConfigs = array(),
        array $blockTypeConfigs = array()
    ) {
        foreach ($blockDefinitionConfigs as $definitionIdentifier => $definitionConfig) {
            if (
                !empty($blockTypeConfigs[$definitionIdentifier]['definition_identifier']) &&
                $blockTypeConfigs[$definitionIdentifier]['definition_identifier'] !== $definitionIdentifier
            ) {
                // We skip the block types which have been completely redefined
                // i.e. had the block definition identifier changed
                continue;
            }

            if (!isset($blockTypeConfigs[$definitionIdentifier])) {
                $blockTypeConfigs[$definitionIdentifier] = array(
                    'name' => $definitionConfig['name'],
                    'enabled' => $definitionConfig['enabled'],
                    'definition_identifier' => $definitionIdentifier,
                    'defaults' => array(),
                );

                continue;
            }

            $blockTypeConfigs[$definitionIdentifier] += array(
                'name' => $definitionConfig['name'],
                'enabled' => $definitionConfig['enabled'],
                'definition_identifier' => $definitionIdentifier,
            );
        }

        foreach ($blockTypeConfigs as $identifier => $blockTypeConfig) {
            if (!$blockTypeConfig['enabled']) {
                continue;
            }

            $serviceIdentifier = sprintf('netgen_block_manager.configuration.block_type.%s', $identifier);

            $container
                ->setDefinition(
                    $serviceIdentifier,
                    new DefinitionDecorator('netgen_block_manager.configuration.block_type')
                )
                ->setArguments(
                    array(
                        $identifier,
                        $blockTypeConfig,
                        new Reference(
                            sprintf(
                                'netgen_block_manager.block.block_definition.%s',
                                $blockTypeConfig['definition_identifier']
                            )
                        ),
                    )
                )
                ->addTag('netgen_block_manager.configuration.block_type')
                ->setAbstract(false);
        }

        return $blockTypeConfigs;
    }

    /**
     * Builds the config objects from provided array config.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array $blockTypeGroupConfigs
     * @param array $blockTypeConfigs
     *
     * @return array
     */
    protected function buildBlockTypeGroups(
        ContainerBuilder $container,
        array $blockTypeGroupConfigs = array(),
        array $blockTypeConfigs = array()
    ) {
        $missingBlockTypes = array();

        // We will add all blocks which are not located in any group to a custom group
        // if it exists and is enabled
        if (isset($blockTypeGroupConfigs['custom']) && $blockTypeGroupConfigs['custom']['enabled']) {
            foreach ($blockTypeConfigs as $blockType => $blockTypeConfig) {
                if (!$blockTypeConfig['enabled']) {
                    continue;
                }

                foreach ($blockTypeGroupConfigs as $blockTypeGroupConfig) {
                    if (in_array($blockType, $blockTypeGroupConfig['block_types'])) {
                        continue 2;
                    }
                }

                $missingBlockTypes[] = $blockType;
            }
        }

        foreach ($blockTypeGroupConfigs as $identifier => $blockTypeGroupConfig) {
            if (!$blockTypeGroupConfig['enabled']) {
                continue;
            }

            $serviceIdentifier = sprintf('netgen_block_manager.configuration.block_type_group.%s', $identifier);

            $blockTypeReferences = array();
            $blockTypes = $blockTypeGroupConfig['block_types'];
            if ($identifier === 'custom') {
                $blockTypes = array_merge($blockTypes, $missingBlockTypes);
            }

            foreach ($blockTypes as $blockType) {
                $blockTypeReferences[] = new Reference(
                    sprintf(
                        'netgen_block_manager.configuration.block_type.%s',
                        $blockType
                    )
                );
            }

            $container
                ->setDefinition(
                    $serviceIdentifier,
                    new DefinitionDecorator('netgen_block_manager.configuration.block_type_group')
                )
                ->setArguments(array($identifier, $blockTypeGroupConfig, $blockTypeReferences))
                ->addTag('netgen_block_manager.configuration.block_type_group')
                ->setAbstract(false);
        }

        return $blockTypeGroupConfigs;
    }
}
