<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Block;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Form;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Config\ConfigDefinitionAwareTrait;
use Netgen\BlockManager\Exception\Block\BlockDefinitionException;
use Netgen\BlockManager\Parameters\ParameterDefinitionCollectionTrait;

final class NullBlockDefinition implements BlockDefinitionInterface
{
    use ParameterDefinitionCollectionTrait;
    use ConfigDefinitionAwareTrait;

    /**
     * @var string
     */
    private $identifier;

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

    public function getIcon(): ?string
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

    public function getViewTypes(): array
    {
        return [];
    }

    public function getViewTypeIdentifiers(): array
    {
        return [];
    }

    public function hasViewType(string $viewType): bool
    {
        return false;
    }

    public function getViewType(string $viewType): ViewType
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
}
