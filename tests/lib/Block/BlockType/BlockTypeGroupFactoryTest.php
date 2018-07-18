<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block\BlockType;

use Netgen\BlockManager\Block\BlockType\BlockType;
use Netgen\BlockManager\Block\BlockType\BlockTypeGroup;
use Netgen\BlockManager\Block\BlockType\BlockTypeGroupFactory;
use Netgen\BlockManager\Tests\TestCase\ExportObjectTrait;
use PHPUnit\Framework\TestCase;

final class BlockTypeGroupFactoryTest extends TestCase
{
    use ExportObjectTrait;

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockTypeGroupFactory::buildBlockTypeGroup
     */
    public function testBuildBlockTypeGroup(): void
    {
        $blockType = BlockType::fromArray(['identifier' => 'title']);

        $blockTypeGroup = BlockTypeGroupFactory::buildBlockTypeGroup(
            'simple_blocks',
            [
                'enabled' => false,
                'name' => 'Simple blocks',
            ],
            [$blockType]
        );

        $this->assertInstanceOf(BlockTypeGroup::class, $blockTypeGroup);

        $this->assertSame(
            [
                'identifier' => 'simple_blocks',
                'isEnabled' => false,
                'name' => 'Simple blocks',
                'blockTypes' => [$blockType],
            ],
            $this->exportObject($blockTypeGroup)
        );
    }
}
