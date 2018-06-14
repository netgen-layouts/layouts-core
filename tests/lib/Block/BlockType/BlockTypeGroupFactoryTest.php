<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block\BlockType;

use Netgen\BlockManager\Block\BlockType\BlockType;
use Netgen\BlockManager\Block\BlockType\BlockTypeGroup;
use Netgen\BlockManager\Block\BlockType\BlockTypeGroupFactory;
use PHPUnit\Framework\TestCase;

final class BlockTypeGroupFactoryTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockTypeGroupFactory::buildBlockTypeGroup
     */
    public function testBuildBlockTypeGroup(): void
    {
        $blockTypeGroup = BlockTypeGroupFactory::buildBlockTypeGroup(
            'simple_blocks',
            [
                'enabled' => false,
                'name' => 'Simple blocks',
            ],
            [new BlockType(['identifier' => 'title'])]
        );

        $this->assertEquals(
            new BlockTypeGroup(
                [
                    'identifier' => 'simple_blocks',
                    'isEnabled' => false,
                    'name' => 'Simple blocks',
                    'blockTypes' => [new BlockType(['identifier' => 'title'])],
                ]
            ),
            $blockTypeGroup
        );
    }
}
