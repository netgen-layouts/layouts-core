<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\BlockDefinition\Configuration;

use Netgen\Layouts\Exception\Block\BlockDefinitionException;
use Netgen\Layouts\Utils\HydratorTrait;

use function array_keys;

final class ViewType
{
    use HydratorTrait;

    private string $identifier;

    private string $name;

    /**
     * @var \Netgen\Layouts\Block\BlockDefinition\Configuration\ItemViewType[]
     */
    private array $itemViewTypes = [];

    /**
     * @var string[]|null
     */
    private ?array $validParameters;

    /**
     * Returns the view type identifier.
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Returns the view type name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the item view types.
     *
     * @return \Netgen\Layouts\Block\BlockDefinition\Configuration\ItemViewType[]
     */
    public function getItemViewTypes(): array
    {
        return $this->itemViewTypes;
    }

    /**
     * Returns the item view type identifiers.
     *
     * @return string[]
     */
    public function getItemViewTypeIdentifiers(): array
    {
        return array_keys($this->itemViewTypes);
    }

    /**
     * Returns the valid parameters.
     *
     * If null is returned, all parameters are valid.
     *
     * @return string[]|null
     */
    public function getValidParameters(): ?array
    {
        return $this->validParameters;
    }

    /**
     * Returns if the view type has an item view type with provided identifier.
     */
    public function hasItemViewType(string $itemViewType): bool
    {
        return isset($this->itemViewTypes[$itemViewType]);
    }

    /**
     * Returns the item view type with provided identifier.
     *
     * @throws \Netgen\Layouts\Exception\Block\BlockDefinitionException If item view type does not exist
     */
    public function getItemViewType(string $itemViewType): ItemViewType
    {
        if (!$this->hasItemViewType($itemViewType)) {
            throw BlockDefinitionException::noItemViewType($this->identifier, $itemViewType);
        }

        return $this->itemViewTypes[$itemViewType];
    }
}
