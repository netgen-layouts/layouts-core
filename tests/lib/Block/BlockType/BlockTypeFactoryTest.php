<?php

namespace Netgen\BlockManager\Tests\Block\BlockType;

use Netgen\BlockManager\Block\BlockType\BlockType;
use Netgen\BlockManager\Block\BlockType\BlockTypeFactory;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use PHPUnit\Framework\TestCase;

class BlockTypeFactoryTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockTypeFactory::buildBlockType
     */
    public function testBuildBlockType()
    {
        $blockDefinition = new BlockDefinition('title');

        $blockType = BlockTypeFactory::buildBlockType(
            'title',
            array(
                'name' => 'Title',
                'icon' => '/icon.svg',
                'enabled' => false,
                'definition_identifier' => 'title',
                'defaults' => array(
                    'viewType' => 'default',
                ),
            ),
            $blockDefinition
        );

        $this->assertEquals(
            new BlockType(
                array(
                    'identifier' => 'title',
                    'isEnabled' => false,
                    'name' => 'Title',
                    'icon' => '/icon.svg',
                    'definition' => $blockDefinition,
                    'defaults' => array(
                        'viewType' => 'default',
                    ),
                )
            ),
            $blockType
        );
    }
}
