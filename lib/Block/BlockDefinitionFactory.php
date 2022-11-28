<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block;

use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\Layouts\Block\BlockDefinition\Configuration\Collection;
use Netgen\Layouts\Block\BlockDefinition\Configuration\ConfigProviderInterface;
use Netgen\Layouts\Block\BlockDefinition\Configuration\Form;
use Netgen\Layouts\Block\BlockDefinition\Configuration\Provider\StaticConfigProvider;
use Netgen\Layouts\Block\BlockDefinition\ContainerDefinitionHandlerInterface;
use Netgen\Layouts\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface;
use Netgen\Layouts\Block\Registry\HandlerPluginRegistry;
use Netgen\Layouts\Config\ConfigDefinitionFactory;
use Netgen\Layouts\Parameters\ParameterBuilderFactoryInterface;

use function get_class;

final class BlockDefinitionFactory
{
    private ParameterBuilderFactoryInterface $parameterBuilderFactory;

    private HandlerPluginRegistry $handlerPluginRegistry;

    private ConfigDefinitionFactory $configDefinitionFactory;

    public function __construct(
        ParameterBuilderFactoryInterface $parameterBuilderFactory,
        HandlerPluginRegistry $handlerPluginRegistry,
        ConfigDefinitionFactory $configDefinitionFactory
    ) {
        $this->parameterBuilderFactory = $parameterBuilderFactory;
        $this->handlerPluginRegistry = $handlerPluginRegistry;
        $this->configDefinitionFactory = $configDefinitionFactory;
    }

    /**
     * Builds the block definition.
     *
     * @param array<string, mixed> $config
     * @param \Netgen\Layouts\Config\ConfigDefinitionHandlerInterface[] $configDefinitionHandlers
     */
    public function buildBlockDefinition(
        string $identifier,
        BlockDefinitionHandlerInterface $handler,
        ?ConfigProviderInterface $configProvider,
        array $config,
        array $configDefinitionHandlers
    ): BlockDefinitionInterface {
        $commonData = $this->getCommonBlockDefinitionData(
            $identifier,
            $handler,
            $configProvider,
            $config,
            $configDefinitionHandlers,
        );

        return BlockDefinition::fromArray($commonData);
    }

    /**
     * Builds the block definition.
     *
     * @param array<string, mixed> $config
     * @param \Netgen\Layouts\Config\ConfigDefinitionHandlerInterface[] $configDefinitionHandlers
     */
    public function buildTwigBlockDefinition(
        string $identifier,
        TwigBlockDefinitionHandlerInterface $handler,
        ?ConfigProviderInterface $configProvider,
        array $config,
        array $configDefinitionHandlers
    ): TwigBlockDefinitionInterface {
        $commonData = $this->getCommonBlockDefinitionData(
            $identifier,
            $handler,
            $configProvider,
            $config,
            $configDefinitionHandlers,
        );

        return TwigBlockDefinition::fromArray($commonData);
    }

    /**
     * Builds the container definition.
     *
     * @param array<string, mixed> $config
     * @param \Netgen\Layouts\Config\ConfigDefinitionHandlerInterface[] $configDefinitionHandlers
     */
    public function buildContainerDefinition(
        string $identifier,
        ContainerDefinitionHandlerInterface $handler,
        ?ConfigProviderInterface $configProvider,
        array $config,
        array $configDefinitionHandlers
    ): ContainerDefinitionInterface {
        $commonData = $this->getCommonBlockDefinitionData(
            $identifier,
            $handler,
            $configProvider,
            $config,
            $configDefinitionHandlers,
        );

        return ContainerDefinition::fromArray($commonData);
    }

    /**
     * Returns the data common to all block definition types.
     *
     * @param array<string, mixed> $config
     * @param \Netgen\Layouts\Config\ConfigDefinitionHandlerInterface[] $configDefinitionHandlers
     *
     * @return array<string, mixed>
     */
    private function getCommonBlockDefinitionData(
        string $identifier,
        BlockDefinitionHandlerInterface $handler,
        ?ConfigProviderInterface $configProvider,
        array $config,
        array $configDefinitionHandlers
    ): array {
        $parameterBuilder = $this->parameterBuilderFactory->createParameterBuilder();
        $handler->buildParameters($parameterBuilder);

        $handlerPlugins = $this->handlerPluginRegistry->getPlugins($identifier, get_class($handler));
        foreach ($handlerPlugins as $handlerPlugin) {
            $handlerPlugin->buildParameters($parameterBuilder);
        }

        $parameterDefinitions = $parameterBuilder->buildParameterDefinitions();

        $configDefinitions = [];
        foreach ($configDefinitionHandlers as $configKey => $configDefinitionHandler) {
            $configDefinitions[$configKey] = $this->configDefinitionFactory->buildConfigDefinition(
                $configKey,
                $configDefinitionHandler,
            );
        }

        return [
            'identifier' => $identifier,
            'handler' => $handler,
            'handlerPlugins' => $handlerPlugins,
            'configProvider' => $configProvider ?? new StaticConfigProvider($identifier, $config),
            'parameterDefinitions' => $parameterDefinitions,
            'configDefinitions' => $configDefinitions,
        ] + $this->processConfig($identifier, $config);
    }

    /**
     * Processes and returns the block definition configuration.
     *
     * @param array<string, mixed> $config
     *
     * @return array<string, mixed>
     */
    private function processConfig(string $identifier, array $config): array
    {
        $collections = [];
        $forms = [];

        foreach (($config['collections'] ?? []) as $collectionIdentifier => $collectionConfig) {
            $collections[$collectionIdentifier] = Collection::fromArray(
                [
                    'identifier' => $collectionIdentifier,
                    'validItemTypes' => $collectionConfig['valid_item_types'],
                    'validQueryTypes' => $collectionConfig['valid_query_types'],
                ],
            );
        }

        foreach (($config['forms'] ?? []) as $formIdentifier => $formConfig) {
            if ($formConfig['enabled'] === false) {
                continue;
            }

            $forms[$formIdentifier] = Form::fromArray(
                [
                    'identifier' => $formIdentifier,
                    'type' => $formConfig['type'],
                ],
            );
        }

        return [
            'name' => $config['name'] ?? '',
            'icon' => $config['icon'] ?? '',
            'isTranslatable' => $config['translatable'] ?? false,
            'collections' => $collections,
            'forms' => $forms,
        ];
    }
}
