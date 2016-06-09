<?php

namespace Netgen\BlockManager\Tests\Configuration\Factory;

use Netgen\BlockManager\Configuration\Factory\BlockTypeFactory;
use Netgen\BlockManager\Configuration\BlockType\BlockType;

class BlockTypeFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \Netgen\BlockManager\Configuration\Factory\BlockTypeFactory::buildBlockType
     */
    public function testBuildBlockType()
    {
        $blockType = BlockTypeFactory::buildBlockType(
            'title',
            array(
                'name' => 'Title',
                'definition_identifier' => 'title',
                'enabled' => true,
                'defaults' => array(
                    'viewType' => 'default',
                ),
            )
        );

        self::assertEquals(
            new BlockType(
                'title',
                true,
                'Title',
                'title',
                array(
                    'viewType' => 'default',
                )
            ),
            $blockType
        );
    }
}
