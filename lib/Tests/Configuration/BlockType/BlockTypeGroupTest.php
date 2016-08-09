<?php

namespace Netgen\BlockManager\Tests\Configuration\BlockType;

use Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup;
use Netgen\BlockManager\Tests\Configuration\Stubs\BlockType;
use PHPUnit\Framework\TestCase;

class BlockTypeGroupTest extends TestCase
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
            array(new BlockType('title'), new BlockType('title_with_h3'))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup::__construct
     * @covers \Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('simple_blocks', $this->blockTypeGroup->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup::isEnabled
     */
    public function testGetIsEnabled()
    {
        $this->assertEquals(true, $this->blockTypeGroup->isEnabled());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup::getName
     */
    public function testGetName()
    {
        $this->assertEquals('Simple blocks', $this->blockTypeGroup->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup::getBlockTypes
     */
    public function testGetBlockTypes()
    {
        $this->assertEquals(
            array(new BlockType('title'), new BlockType('title_with_h3')),
            $this->blockTypeGroup->getBlockTypes()
        );
    }
}
