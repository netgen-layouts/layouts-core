<?php

namespace Netgen\BlockManager\Tests\Configuration\Factory;

use Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup;
use Netgen\BlockManager\Configuration\Factory\BlockTypeGroupFactory;
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
