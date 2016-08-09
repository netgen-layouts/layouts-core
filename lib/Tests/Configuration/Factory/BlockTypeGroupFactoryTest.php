<?php

namespace Netgen\BlockManager\Tests\Configuration\Factory;

use Netgen\BlockManager\Configuration\Factory\BlockTypeGroupFactory;
use Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup;
use Netgen\BlockManager\Tests\Configuration\Stubs\BlockType;
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
            ),
            array(new BlockType('title'), new BlockType('title_with_h3'))
        );

        $this->assertEquals(
            new BlockTypeGroup(
                'simple_blocks',
                true,
                'Simple blocks',
                array(new BlockType('title'), new BlockType('title_with_h3'))
            ),
            $blockTypeGroup
        );
    }
}
