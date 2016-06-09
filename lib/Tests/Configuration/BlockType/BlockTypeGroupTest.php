<?php

namespace Netgen\BlockManager\Tests\Configuration\BlockType;

use Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup;

class BlockTypeGroupTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup
     */
    protected $blockTypeGroup;

    public function setUp()
    {
        $this->blockTypeGroup = new BlockTypeGroup(
            'simple_blocks',
            true,
            'Simple blocks',
            array('title', 'title_with_h3')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup::__construct
     * @covers \Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup::getIdentifier
     */
    public function testGetIdentifier()
    {
        self::assertEquals('simple_blocks', $this->blockTypeGroup->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup::isEnabled
     */
    public function testGetIsEnabled()
    {
        self::assertEquals(true, $this->blockTypeGroup->isEnabled());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup::getName
     */
    public function testGetName()
    {
        self::assertEquals('Simple blocks', $this->blockTypeGroup->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup::getBlockTypes
     */
    public function testGetBlockTypes()
    {
        self::assertEquals(array('title', 'title_with_h3'), $this->blockTypeGroup->getBlockTypes());
    }
}
