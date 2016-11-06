<?php

namespace Netgen\BlockManager\Configuration\Factory;

use Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup;

class BlockTypeGroupFactory
{
    /**
     * Builds the block type group.
     *
     * @param string $identifier
     * @param array $config
     * @param \Netgen\BlockManager\Configuration\BlockType\BlockType[] $blockTypes
     *
     * @return \Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup
     */
    public static function buildBlockTypeGroup($identifier, array $config, array $blockTypes = array())
    {
        return new BlockTypeGroup(
            array(
                'identifier' => $identifier,
                'name' => $config['name'],
                'blockTypes' => $blockTypes,
            )
        );
    }
}
