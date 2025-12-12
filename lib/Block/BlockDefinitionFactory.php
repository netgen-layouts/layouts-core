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
use Netgen\Layouts\Parameters\ParameterBuilderFactory;

final class BlockDefinitionFactory
{
    public function __construct(
        private ParameterBuilderFactory $parameterBuilderFactory,
        private HandlerPluginRegistry $handlerPluginRegistry,
        private ConfigDefinitionFactory $configDefinitionFactory,
    ) {}

    /**
     * Builds the block definition.
     *
     * @param \Netgen\Layouts\Config\ConfigDefinitionHandlerInterface[] $configDefinitionHandlers
     * @param array<string, mixed> $config
     */
    public function buildBlockDefinition(
        string $identifier,
        BlockDefinitionHandlerInterface $handler,
        array $configDefinitionHandlers,
        ?ConfigProviderInterface $configProvider,
        array $config,
    ): BlockDefinitionInterface {
        $commonData = $this->getCommonBlockDefinitionData(
            $identifier,
            $handler,
            $configDefinitionHandlers,
            $configProvider,
            $config,
        );

        return BlockDefinition::fromArray($commonData);
    }

    /**
     * Builds the block definition.
     *
     * @param \Netgen\Layouts\Config\ConfigDefinitionHandlerInterface[] $configDefinitionHandlers
     * @param array<string, mixed> $config
     */
    public function buildTwigBlockDefinition(
        string $identifier,
        TwigBlockDefinitionHandlerInterface $handler,
        array $configDefinitionHandlers,
        ?ConfigProviderInterface $configProvider,
        array $config,
    ): TwigBlockDefinitionInterface {
        $commonData = $this->getCommonBlockDefinitionData(
            $identifier,
            $handler,
            $configDefinitionHandlers,
            $configProvider,
            $config,
        );

        return TwigBlockDefinition::fromArray($commonData);
    }

    /**
     * Builds the container definition.
     *
     * @param \Netgen\Layouts\Config\ConfigDefinitionHandlerInterface[] $configDefinitionHandlers
     * @param array<string, mixed> $config
     */
    public function buildContainerDefinition(
        string $identifier,
        ContainerDefinitionHandlerInterface $handler,
        array $configDefinitionHandlers,
        ?ConfigProviderInterface $configProvider,
        array $config,
    ): ContainerDefinitionInterface {
        $commonData = $this->getCommonBlockDefinitionData(
            $identifier,
            $handler,
            $configDefinitionHandlers,
            $configProvider,
            $config,
        );

        return ContainerDefinition::fromArray($commonData);
    }

    /**
     * Returns the data common to all block definition types.
     *
     * @param \Netgen\Layouts\Config\ConfigDefinitionHandlerInterface[] $configDefinitionHandlers
     * @param array<string, mixed> $config
     *
     * @return array<string, mixed>
     */
    private function getCommonBlockDefinitionData(
        string $identifier,
        BlockDefinitionHandlerInterface $handler,
        array $configDefinitionHandlers,
        ?ConfigProviderInterface $configProvider,
        array $config,
    ): array {
        $parameterBuilder = $this->parameterBuilderFactory->createParameterBuilder();
        $handler->buildParameters($parameterBuilder);

        $handlerPlugins = $this->handlerPluginRegistry->getPlugins($identifier, $handler::class);
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
        ] + $this->processConfig($config);
    }

    /**
     * Processes and returns the block definition configuration.
     *
     * @param array<string, mixed> $config
     *
     * @return array<string, mixed>
     */
    private function processConfig(array $config): array
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
