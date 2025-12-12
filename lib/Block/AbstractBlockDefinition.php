<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition\Configuration\Collection;
use Netgen\Layouts\Block\BlockDefinition\Configuration\ConfigProviderInterface;
use Netgen\Layouts\Block\BlockDefinition\Configuration\Form;
use Netgen\Layouts\Block\BlockDefinition\Configuration\ViewType;
use Netgen\Layouts\Block\BlockDefinition\Handler\PluginInterface;
use Netgen\Layouts\Config\ConfigDefinitionAwareTrait;
use Netgen\Layouts\Exception\Block\BlockDefinitionException;
use Netgen\Layouts\Parameters\ParameterDefinitionCollectionTrait;
use Netgen\Layouts\Utils\HydratorTrait;

use function array_any;
use function array_key_exists;
use function array_keys;

abstract class AbstractBlockDefinition implements BlockDefinitionInterface
{
    use ConfigDefinitionAwareTrait;
    use HydratorTrait;
    use ParameterDefinitionCollectionTrait;

    final public protected(set) string $identifier;

    final public protected(set) string $name;

    final public protected(set) ?string $icon;

    /**
     * @var \Netgen\Layouts\Block\BlockDefinition\Handler\PluginInterface[]
     */
    final public protected(set) array $handlerPlugins = [];

    final public protected(set) bool $isTranslatable;

    /**
     * @var \Netgen\Layouts\Block\BlockDefinition\Configuration\Collection[]
     */
    final public protected(set) array $collections = [];

    /**
     * @var \Netgen\Layouts\Block\BlockDefinition\Configuration\Form[]
     */
    final public protected(set) array $forms = [];

    final public array $viewTypes {
        get => $this->configProvider->provideViewTypes();
    }

    final public array $viewTypeIdentifiers {
        get => array_keys($this->configProvider->provideViewTypes());
    }

    final protected ConfigProviderInterface $configProvider;

    final public function hasCollection(string $identifier): bool
    {
        return array_key_exists($identifier, $this->collections);
    }

    final public function getCollection(string $identifier): Collection
    {
        if (!$this->hasCollection($identifier)) {
            throw BlockDefinitionException::noCollection($this->identifier, $identifier);
        }

        return $this->collections[$identifier];
    }

    final public function hasForm(string $formName): bool
    {
        return array_key_exists($formName, $this->forms);
    }

    final public function getForm(string $formName): Form
    {
        if (!$this->hasForm($formName)) {
            throw BlockDefinitionException::noForm($this->identifier, $formName);
        }

        return $this->forms[$formName];
    }

    final public function getBlockViewTypes(Block $block): array
    {
        return $this->configProvider->provideViewTypes($block);
    }

    final public function getBlockViewTypeIdentifiers(Block $block): array
    {
        return array_keys($this->configProvider->provideViewTypes($block));
    }

    final public function hasViewType(string $viewType, ?Block $block = null): bool
    {
        return array_key_exists($viewType, $this->configProvider->provideViewTypes($block));
    }

    final public function getViewType(string $viewType, ?Block $block = null): ViewType
    {
        $viewTypes = $this->configProvider->provideViewTypes($block);

        if (!array_key_exists($viewType, $viewTypes)) {
            throw BlockDefinitionException::noViewType($this->identifier, $viewType);
        }

        return $viewTypes[$viewType];
    }

    final public function getDynamicParameters(Block $block): DynamicParameters
    {
        $dynamicParams = new DynamicParameters();

        $this->handler->getDynamicParameters($dynamicParams, $block);

        foreach ($this->handlerPlugins as $handlerPlugin) {
            $handlerPlugin->getDynamicParameters($dynamicParams, $block);
        }

        return $dynamicParams;
    }

    final public function isContextual(Block $block): bool
    {
        return $this->handler->isContextual($block);
    }

    final public function hasHandlerPlugin(string $className): bool
    {
        return array_any(
            $this->handlerPlugins,
            static fn (PluginInterface $handlerPlugin): bool => $handlerPlugin instanceof $className,
        );
    }

    final public function getHandlerPlugin(string $className): PluginInterface
    {
        foreach ($this->handlerPlugins as $handlerPlugin) {
            if ($handlerPlugin instanceof $className) {
                return $handlerPlugin;
            }
        }

        throw BlockDefinitionException::noPlugin($this->identifier, $className);
    }
}
