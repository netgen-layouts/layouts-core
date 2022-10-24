<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\BlockType;

use Netgen\Layouts\Utils\HydratorTrait;

use function array_filter;
use function array_values;

/**
 * Block type group is used to group together a list of block types for
 * grouped display in the app interface.
 *
 * @final
 */
class BlockTypeGroup
{
    use HydratorTrait;

    private string $identifier;

    private bool $isEnabled;

    private int $priority;

    private string $name;

    /**
     * @var \Netgen\Layouts\Block\BlockType\BlockType[]
     */
    private array $blockTypes = [];

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
     * Returns the priority of the block type group.
     */
    public function getPriority(): int
    {
        return $this->priority;
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
     * @return \Netgen\Layouts\Block\BlockType\BlockType[]
     */
    public function getBlockTypes(bool $onlyEnabled = false): array
    {
        if (!$onlyEnabled) {
            return $this->blockTypes;
        }

        return array_values(
            array_filter(
                $this->blockTypes,
                static fn (BlockType $blockType): bool => $blockType->isEnabled(),
            ),
        );
    }
}
