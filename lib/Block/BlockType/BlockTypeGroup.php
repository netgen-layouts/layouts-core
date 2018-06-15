<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Block\BlockType;

use Netgen\BlockManager\Value;

/**
 * Block type group is used to group together a list of block types for
 * grouped display in the app interface.
 *
 * @final
 */
class BlockTypeGroup extends Value
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var bool
     */
    protected $isEnabled;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Netgen\BlockManager\Block\BlockType\BlockType[]
     */
    protected $blockTypes = [];

    /**
     * Returns the block type group identifier.
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Returns if the block type group is enabled or not.
     */
    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    /**
     * Returns the block type group name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the block types in this group.
     *
     * @param bool $onlyEnabled
     *
     * @return \Netgen\BlockManager\Block\BlockType\BlockType[]
     */
    public function getBlockTypes(bool $onlyEnabled = false): array
    {
        if (!$onlyEnabled) {
            return $this->blockTypes;
        }

        return array_values(
            array_filter(
                $this->blockTypes,
                function (BlockType $blockType): bool {
                    return $blockType->isEnabled();
                }
            )
        );
    }
}
