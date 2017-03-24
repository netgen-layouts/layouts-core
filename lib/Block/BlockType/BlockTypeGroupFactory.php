<?php

namespace Netgen\BlockManager\Block\BlockType;

class BlockTypeGroupFactory
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
