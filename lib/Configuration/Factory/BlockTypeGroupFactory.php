<?php

namespace Netgen\BlockManager\Configuration\Factory;

use Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup;

class BlockTypeGroupFactory
{
    /**
     * Builds the block type group.
     *
     * @param array $config
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\Configuration\BlockType\BlockType
     */
    public static function buildBlockTypeGroup(array $config, $identifier)
    {
        return new BlockTypeGroup(
            $identifier,
            $config['name'],
            $config['block_types']
        );
    }
}
