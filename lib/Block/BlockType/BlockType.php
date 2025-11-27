<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\BlockType;

use Netgen\Layouts\Block\BlockDefinitionInterface;
use Netgen\Layouts\Utils\HydratorTrait;

/**
 * Block type represents the starting configuration of the block. E.g. while
 * block definition specifies what view types the block can have, block type
 * specifies the exact view type the new block will have once created. The
 * same goes for item view types and all of the block parameters.
 */
final class BlockType
{
    use HydratorTrait;

    /**
     * Returns the block type identifier.
     */
    public private(set) string $identifier;

    /**
     * Returns if the block type is enabled or not.
     */
    public private(set) bool $isEnabled;

    /**
     * Returns the block type name.
     */
    public private(set) string $name;

    /**
     * Returns the block type icon.
     */
    public private(set) ?string $icon;

    /**
     * Returns the block definition.
     */
    public private(set) BlockDefinitionInterface $definition;

    /**
     * Returns the default block values.
     *
     * @var array<string, mixed>
     */
    public private(set) array $defaults = [];

    /**
     * Returns the default block name.
     */
    public string $defaultName {
        get => $this->defaults['name'] ?? '';
    }

    /**
     * Returns the default block view type.
     */
    public string $defaultViewType {
        get => $this->defaults['view_type'] ?? '';
    }

    /**
     * Returns the default block item view type.
     */
    public string $defaultItemViewType {
        get => $this->defaults['item_view_type'] ?? '';
    }

    /**
     * Returns the default block parameters.
     *
     * @var array<string, mixed>
     */
    public array $defaultParameters {
        get => $this->defaults['parameters'] ?? [];
    }
}
