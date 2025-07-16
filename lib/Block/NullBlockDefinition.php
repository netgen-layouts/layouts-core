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

    private string $identifier;

    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getName(): string
    {
        return 'Invalid block definition';
    }

    public function getIcon(): string
    {
        return '';
    }

    public function isTranslatable(): bool
    {
        return false;
    }

    public function getCollections(): array
    {
        return [];
    }

    public function hasCollection(string $identifier): bool
    {
        return false;
    }

    public function getCollection(string $identifier): Collection
    {
        throw BlockDefinitionException::noCollection($this->identifier, $identifier);
    }

    public function getForms(): array
    {
        return [];
    }

    public function hasForm(string $formName): bool
    {
        return false;
    }

    public function getForm(string $formName): Form
    {
        throw BlockDefinitionException::noForm($this->identifier, $formName);
    }

    public function getViewTypes(?Block $block = null): array
    {
        return [];
    }

    public function getViewTypeIdentifiers(?Block $block = null): array
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

    public function hasPlugin(string $className): bool
    {
        return false;
    }

    public function getPlugin(string $className): PluginInterface
    {
        throw BlockDefinitionException::noPlugin($this->identifier, $className);
    }

    public function getPlugins(): array
    {
        return [];
    }

    public function getHandler(): BlockDefinitionHandlerInterface
    {
        return new class extends BlockDefinitionHandler {};
    }
}
