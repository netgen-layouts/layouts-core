<?php

namespace Netgen\BlockManager\Tests\Block\BlockType;

use Netgen\BlockManager\Block\BlockType\BlockTypeGroup;
use Netgen\BlockManager\Block\BlockType\BlockTypeGroupFactory;
use Netgen\BlockManager\Tests\Block\Stubs\BlockType;
use PHPUnit\Framework\TestCase;

class BlockTypeGroupFactoryTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockTypeGroupFactory::buildBlockTypeGroup
     */
    public function testBuildBlockTypeGroup()
    {
        $blockTypeGroup = BlockTypeGroupFactory::buildBlockTypeGroup(
            'simple_blocks',
            array(
                'name' => 'Simple blocks',
            ),
            array(new BlockType(array('identifier' => 'title')))
        );

        $this->assertEquals(
            new BlockTypeGroup(
                array(
                    'identifier' => 'simple_blocks',
                    'name' => 'Simple blocks',
                    'blockTypes' => array(new BlockType(array('identifier' => 'title'))),
                )
            ),
            $blockTypeGroup
        );
    }
}
