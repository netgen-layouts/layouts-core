<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Block\BlockDefinition\Configuration;

use Netgen\BlockManager\Exception\Block\BlockDefinitionException;
use Netgen\BlockManager\Value;

final class ViewType extends Value
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType[]
     */
    protected $itemViewTypes = [];

    /**
     * @var array|null
     */
    protected $validParameters;

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
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType[]
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
     * @throws \Netgen\BlockManager\Exception\Block\BlockDefinitionException If item view type does not exist
     */
    public function getItemViewType(string $itemViewType): ItemViewType
    {
        if (!$this->hasItemViewType($itemViewType)) {
            throw BlockDefinitionException::noItemViewType($this->identifier, $itemViewType);
        }

        return $this->itemViewTypes[$itemViewType];
    }
}
