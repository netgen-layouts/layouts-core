<?php

namespace Netgen\BlockManager\Block\BlockType;

use Netgen\BlockManager\Block\BlockDefinitionInterface;

class BlockTypeFactory
{
    /**
     * Builds the block type.
     *
     * @param string $identifier
     * @param array $config
     * @param \Netgen\BlockManager\Block\BlockDefinitionInterface $blockDefinition
     *
     * @return \Netgen\BlockManager\Block\BlockType\BlockType
     */
    public static function buildBlockType($identifier, array $config, BlockDefinitionInterface $blockDefinition)
    {
        return new BlockType(
            array(
                'identifier' => $identifier,
                'name' => $config['name'],
                'definition' => $blockDefinition,
                'defaults' => $config['defaults'],
            )
        );
    }
}
