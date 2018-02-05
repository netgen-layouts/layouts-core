<?php

namespace Netgen\BlockManager\Block;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Form;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Block\BlockDefinition\ContainerDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\Registry\HandlerPluginRegistryInterface;
use Netgen\BlockManager\Config\ConfigDefinitionFactory;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Parameters\ParameterBuilderFactoryInterface;

final class BlockDefinitionFactory
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterBuilderFactoryInterface
     */
    private $parameterBuilderFactory;

    /**
     * @var \Netgen\BlockManager\Block\Registry\HandlerPluginRegistryInterface
     */
    private $handlerPluginRegistry;

    /**
     * @var \Netgen\BlockManager\Config\ConfigDefinitionFactory
     */
    private $configDefinitionFactory;

    public function __construct(
        ParameterBuilderFactoryInterface $parameterBuilderFactory,
        HandlerPluginRegistryInterface $handlerPluginRegistry,
        ConfigDefinitionFactory $configDefinitionFactory
    ) {
        $this->parameterBuilderFactory = $parameterBuilderFactory;
        $this->handlerPluginRegistry = $handlerPluginRegistry;
        $this->configDefinitionFactory = $configDefinitionFactory;
    }

    /**
     * Builds the block definition.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface $handler
     * @param array $config
     * @param \Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface[] $configDefinitionHandlers
     *
     * @return \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    public function buildBlockDefinition(
        $identifier,
        BlockDefinitionHandlerInterface $handler,
        array $config,
        array $configDefinitionHandlers
    ) {
        $commonData = $this->getCommonBlockDefinitionData(
            $identifier,
            $handler,
            $config,
            $configDefinitionHandlers
        );

        return new BlockDefinition($commonData);
    }

    /**
     * Builds the block definition.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface $handler
     * @param array $config
     * @param \Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface[] $configDefinitionHandlers
     *
     * @return \Netgen\BlockManager\Block\TwigBlockDefinitionInterface
     */
    public function buildTwigBlockDefinition(
        $identifier,
        TwigBlockDefinitionHandlerInterface $handler,
        array $config,
        array $configDefinitionHandlers
    ) {
        $commonData = $this->getCommonBlockDefinitionData(
            $identifier,
            $handler,
            $config,
            $configDefinitionHandlers
        );

        return new TwigBlockDefinition($commonData);
    }

    /**
     * Builds the container definition.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Block\BlockDefinition\ContainerDefinitionHandlerInterface $handler
     * @param array $config
     * @param \Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface[] $configDefinitionHandlers
     *
     * @return \Netgen\BlockManager\Block\ContainerDefinitionInterface
     */
    public function buildContainerDefinition(
        $identifier,
        ContainerDefinitionHandlerInterface $handler,
        array $config,
        array $configDefinitionHandlers
    ) {
        $commonData = $this->getCommonBlockDefinitionData(
            $identifier,
            $handler,
            $config,
            $configDefinitionHandlers
        );

        return new ContainerDefinition($commonData);
    }

    /**
     * Returns the data common to all block definition types.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface $handler
     * @param array $config
     * @param \Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface[] $configDefinitionHandlers
     *
     * @return array
     */
    private function getCommonBlockDefinitionData(
        $identifier,
        BlockDefinitionHandlerInterface $handler,
        array $config,
        array $configDefinitionHandlers
    ) {
        $parameterBuilder = $this->parameterBuilderFactory->createParameterBuilder();
        $handler->buildParameters($parameterBuilder);

        $handlerPlugins = $this->handlerPluginRegistry->getPlugins(get_class($handler));
        foreach ($handlerPlugins as $handlerPlugin) {
            $handlerPlugin->buildParameters($parameterBuilder);
        }

        $parameters = $parameterBuilder->buildParameters();

        $configDefinitions = array();
        foreach ($configDefinitionHandlers as $configKey => $configDefinitionHandler) {
            $configDefinitions[$configKey] = $this->configDefinitionFactory->buildConfigDefinition(
                $configKey,
                $configDefinitionHandler
            );
        }

        return array(
            'identifier' => $identifier,
            'handler' => $handler,
            'handlerPlugins' => $handlerPlugins,
            'parameters' => $parameters,
            'configDefinitions' => $configDefinitions,
        ) + $this->processConfig($identifier, $config);
    }

    /**
     * Processes and returns the block definition configuration.
     *
     * @param string $identifier
     * @param array $config
     *
     * @return array
     */
    private function processConfig($identifier, array $config)
    {
        $collections = array();
        $forms = array();
        $viewTypes = array();

        if (isset($config['collections'])) {
            foreach ($config['collections'] as $collectionIdentifier => $collectionConfig) {
                $collections[$collectionIdentifier] = new Collection(
                    array(
                        'identifier' => $collectionIdentifier,
                        'validItemTypes' => $collectionConfig['valid_item_types'],
                        'validQueryTypes' => $collectionConfig['valid_query_types'],
                    )
                );
            }
        }

        if (isset($config['forms'])) {
            foreach ($config['forms'] as $formIdentifier => $formConfig) {
                if (!$formConfig['enabled']) {
                    continue;
                }

                $forms[$formIdentifier] = new Form(
                    array(
                        'identifier' => $formIdentifier,
                        'type' => $formConfig['type'],
                    )
                );
            }
        }

        if (isset($config['view_types'])) {
            foreach ($config['view_types'] as $viewTypeIdentifier => $viewTypeConfig) {
                if (!$viewTypeConfig['enabled']) {
                    continue;
                }

                $itemViewTypes = array();

                if (!isset($viewTypeConfig['item_view_types']['standard'])) {
                    $viewTypeConfig['item_view_types'] = array(
                        'standard' => array(
                            'name' => 'Standard',
                            'enabled' => true,
                        ),
                    ) + $viewTypeConfig['item_view_types'];
                }

                foreach ($viewTypeConfig['item_view_types'] as $itemViewTypeIdentifier => $itemViewTypeConfig) {
                    if (!$itemViewTypeConfig['enabled']) {
                        continue;
                    }

                    $itemViewTypes[$itemViewTypeIdentifier] = new ItemViewType(
                        array(
                            'identifier' => $itemViewTypeIdentifier,
                            'name' => $itemViewTypeConfig['name'],
                        )
                    );
                }

                if (empty($itemViewTypes)) {
                    throw new RuntimeException(
                        sprintf(
                            'You need to specify at least one enabled item view type for "%s" view type and "%s" block definition.',
                            $viewTypeIdentifier,
                            $identifier
                        )
                    );
                }

                $viewTypes[$viewTypeIdentifier] = new ViewType(
                    array(
                        'identifier' => $viewTypeIdentifier,
                        'name' => isset($viewTypeConfig['name']) ? $viewTypeConfig['name'] : '',
                        'itemViewTypes' => $itemViewTypes,
                        'validParameters' => array_key_exists('valid_parameters', $viewTypeConfig) ? $viewTypeConfig['valid_parameters'] : null,
                    )
                );
            }
        }

        if (empty($viewTypes)) {
            throw new RuntimeException(
                sprintf(
                    'You need to specify at least one enabled view type for "%s" block definition.',
                    $identifier
                )
            );
        }

        return array(
            'name' => isset($config['name']) ? $config['name'] : '',
            'icon' => isset($config['icon']) ? $config['icon'] : '',
            'isTranslatable' => isset($config['translatable']) ? $config['translatable'] : false,
            'collections' => $collections,
            'forms' => $forms,
            'viewTypes' => $viewTypes,
        );
    }
}
