<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\BlockType;

use Netgen\Layouts\Block\BlockDefinitionInterface;

final class BlockTypeFactory
{
    /**
     * Builds the block type.
     *
     * @param array<string, mixed> $config
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
            ],
        );
    }
}
