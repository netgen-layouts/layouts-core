<?php

namespace Netgen\BlockManager\Configuration\Factory;

use Netgen\BlockManager\Configuration\BlockType\BlockType;

class BlockTypeFactory
{
    /**
     * Builds the block type.
     *
     * @param string $identifier
     * @param array $config
     *
     * @return \Netgen\BlockManager\Configuration\BlockType\BlockType
     */
    public static function buildBlockType($identifier, array $config)
    {
        return new BlockType(
            $identifier,
            $config['enabled'],
            $config['name'],
            $config['definition_identifier'],
            $config['defaults']
        );
    }
}
