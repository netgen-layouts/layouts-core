<?php

namespace Netgen\BlockManager\Configuration\Factory;

use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Configuration\BlockType\BlockType;

class BlockTypeFactory
{
    /**
     * Builds the block type.
     *
     * @param string $identifier
     * @param array $config
     * @param \Netgen\BlockManager\Block\BlockDefinitionInterface $blockDefinition
     *
     * @return \Netgen\BlockManager\Configuration\BlockType\BlockType
     */
    public static function buildBlockType($identifier, array $config, BlockDefinitionInterface $blockDefinition)
    {
        return new BlockType(
            array(
                'identifier' => $identifier,
                'name' => $config['name'],
                'blockDefinition' => $blockDefinition,
                'defaults' => $config['defaults'],
            )
        );
    }
}
