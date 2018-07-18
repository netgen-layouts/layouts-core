<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Block\BlockType;

final class BlockTypeGroupFactory
{
    /**
     * Builds the block type group.
     *
     * @param string $identifier
     * @param array $config
     * @param \Netgen\BlockManager\Block\BlockType\BlockType[] $blockTypes
     *
     * @return \Netgen\BlockManager\Block\BlockType\BlockTypeGroup
     */
    public static function buildBlockTypeGroup(string $identifier, array $config, array $blockTypes = []): BlockTypeGroup
    {
        return BlockTypeGroup::fromArray(
            [
                'identifier' => $identifier,
                'isEnabled' => $config['enabled'],
                'name' => $config['name'],
                'blockTypes' => $blockTypes,
            ]
        );
    }
}
