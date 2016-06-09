<?php

namespace Netgen\BlockManager\Tests\Configuration\Factory;

use Netgen\BlockManager\Configuration\Factory\BlockTypeGroupFactory;
use Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup;
use PHPUnit\Framework\TestCase;

class BlockTypeGroupFactoryTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Configuration\Factory\BlockTypeGroupFactory::buildBlockTypeGroup
     */
    public function testBuildBlockTypeGroup()
    {
        $blockTypeGroup = BlockTypeGroupFactory::buildBlockTypeGroup(
            'simple_blocks',
            array(
                'name' => 'Simple blocks',
                'enabled' => true,
                'block_types' => array('title', 'title_with_h3'),
            )
        );

        self::assertEquals(
            new BlockTypeGroup(
                'simple_blocks',
                true,
                'Simple blocks',
                array('title', 'title_with_h3')
            ),
            $blockTypeGroup
        );
    }
}
