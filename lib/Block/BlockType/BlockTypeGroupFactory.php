<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\BlockType;

final class BlockTypeGroupFactory
{
    /**
     * Builds the block type group.
     *
     * @param array<string, mixed> $config
     * @param \Netgen\Layouts\Block\BlockType\BlockType[] $blockTypes
     */
    public static function buildBlockTypeGroup(string $identifier, array $config, array $blockTypes): BlockTypeGroup
    {
        return BlockTypeGroup::fromArray(
            [
                'identifier' => $identifier,
                'isEnabled' => $config['enabled'],
                'priority' => $config['priority'],
                'name' => $config['name'],
                'blockTypes' => $blockTypes,
            ],
        );
    }
}
