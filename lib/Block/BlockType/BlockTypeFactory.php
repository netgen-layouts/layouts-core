<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Block\BlockType;

use Netgen\BlockManager\Block\BlockDefinitionInterface;

final class BlockTypeFactory
{
    /**
     * Builds the block type.
     */
    public static function buildBlockType(string $identifier, array $config, BlockDefinitionInterface $blockDefinition): BlockType
    {
        return BlockType::fromArray(
            [
                'identifier' => $identifier,
                'isEnabled' => $config['enabled'],
                'name' => $config['name'],
                'icon' => $config['icon'],
                'definition' => $blockDefinition,
                'defaults' => $config['defaults'],
            ]
        );
    }
}
