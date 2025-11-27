<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\BlockType;

use Netgen\Layouts\Utils\HydratorTrait;

use function array_filter;
use function array_values;

/**
 * Block type group is used to group together a list of block types for
 * grouped display in the app interface.
 */
final class BlockTypeGroup
{
    use HydratorTrait;

    /**
     * Returns the block type group identifier.
     */
    public private(set) string $identifier;

    /**
     * Returns if the block type group is enabled or not.
     */
    public private(set) bool $isEnabled;

    /**
     * Returns the priority of the block type group.
     */
    public private(set) int $priority;

    /**
     * Returns the block type group name.
     */
    public private(set) string $name;

    /**
     * Returns the block types in this group.
     *
     * @var \Netgen\Layouts\Block\BlockType\BlockType[]
     */
    public private(set) array $blockTypes = [];

    /**
     * Returns enabled block types in this group.
     *
     * @var \Netgen\Layouts\Block\BlockType\BlockType[]
     */
    public array $enabledBlockTypes {
        get {
            return array_values(
                array_filter(
                    $this->blockTypes,
                    static fn (BlockType $blockType): bool => $blockType->isEnabled,
                ),
            );
        }
    }
}
