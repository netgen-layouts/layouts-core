<?php

namespace Netgen\BlockManager\Configuration\Factory;

use Netgen\BlockManager\Configuration\BlockType\BlockType;

class BlockTypeFactory
{
    /**
     * Builds the block type.
     *
     * @param array $config
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\Configuration\BlockType\BlockType
     */
    public static function buildBlockType(array $config, $identifier)
    {
        return new BlockType(
            $identifier,
            $config['name'],
            $config['definition_identifier'],
            $config['defaults']
        );
    }
}
