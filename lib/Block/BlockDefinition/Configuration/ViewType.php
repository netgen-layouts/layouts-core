<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\BlockDefinition\Configuration;

use Netgen\Layouts\Exception\Block\BlockDefinitionException;
use Netgen\Layouts\Utils\HydratorTrait;

use function array_keys;

final class ViewType
{
    use HydratorTrait;

    /**
     * Returns the view type identifier.
     */
    public private(set) string $identifier;

    /**
     * Returns the view type name.
     */
    public private(set) string $name;

    /**
     * Returns the item view types.
     *
     * @var \Netgen\Layouts\Block\BlockDefinition\Configuration\ItemViewType[]
     */
    public private(set) array $itemViewTypes = [];

    /**
     * Returns the item view type identifiers.
     *
     * @var string[]
     */
    public array $itemViewTypeIdentifiers {
        get => array_keys($this->itemViewTypes);
    }

    /**
     * Returns the valid parameters.
     *
     * If null is returned, all parameters are valid.
     *
     * @var string[]|null
     */
    public private(set) ?array $validParameters;

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
