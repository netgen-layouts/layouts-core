<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\Layouts\Block\BlockDefinition\Configuration\Collection;
use Netgen\Layouts\Block\BlockDefinition\Configuration\Form;
use Netgen\Layouts\Block\BlockDefinition\Configuration\ViewType;
use Netgen\Layouts\Block\BlockDefinition\Handler\PluginInterface;
use Netgen\Layouts\Config\ConfigDefinitionAwareTrait;
use Netgen\Layouts\Exception\Block\BlockDefinitionException;
use Netgen\Layouts\Parameters\ParameterDefinitionCollectionTrait;

final class NullBlockDefinition implements BlockDefinitionInterface
{
    use ConfigDefinitionAwareTrait;
    use ParameterDefinitionCollectionTrait;

    public string $name {
        get => 'Invalid block definition';
    }

    public string $icon {
        get => '';
    }

    public BlockDefinitionHandlerInterface $handler {
        get => new class extends BlockDefinitionHandler {};
    }

    public array $handlerPlugins {
        get => [];
    }

    public false $isTranslatable {
        get => false;
    }

    public array $collections {
        get => [];
    }

    public array $forms {
        get => [];
    }

    public array $viewTypes {
        get => [];
    }

    public array $viewTypeIdentifiers {
        get => [];
    }

    public function __construct(
        public private(set) string $identifier,
    ) {}

    public function hasCollection(string $identifier): bool
    {
        return false;
    }

    public function getCollection(string $identifier): Collection
    {
        throw BlockDefinitionException::noCollection($this->identifier, $identifier);
    }

    public function hasForm(string $formName): bool
    {
        return false;
    }

    public function getForm(string $formName): Form
    {
        throw BlockDefinitionException::noForm($this->identifier, $formName);
    }

    public function getBlockViewTypes(Block $block): array
    {
        return [];
    }

    public function getBlockViewTypeIdentifiers(Block $block): array
    {
        return [];
    }

    public function hasViewType(string $viewType, ?Block $block = null): bool
    {
        return false;
    }

    public function getViewType(string $viewType, ?Block $block = null): ViewType
    {
        throw BlockDefinitionException::noViewType($this->identifier, $viewType);
    }

    public function getDynamicParameters(Block $block): DynamicParameters
    {
        return new DynamicParameters();
    }

    public function isContextual(Block $block): bool
    {
        return false;
    }

    public function hasHandlerPlugin(string $className): bool
    {
        return false;
    }

    public function getHandlerPlugin(string $className): PluginInterface
    {
        throw BlockDefinitionException::noPlugin($this->identifier, $className);
    }
}
