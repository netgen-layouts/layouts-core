<?php

namespace Netgen\BlockManager\Tests\Block\Registry;

use Netgen\BlockManager\Block\BlockType\BlockTypeGroup;
use Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry;
use PHPUnit\Framework\TestCase;

class BlockTypeGroupRegistryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockType\BlockTypeGroup
     */
    protected $blockTypeGroup;

    /**
     * @var \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry
     */
    protected $registry;

    public function setUp()
    {
        $this->registry = new BlockTypeGroupRegistry();

        $this->blockTypeGroup = new BlockTypeGroup(array('identifier' => 'block_type_group'));

        $this->registry->addBlockTypeGroup('block_type_group', $this->blockTypeGroup);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::addBlockTypeGroup
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::getBlockTypeGroups
     */
    public function testAddBlockTypeGroup()
    {
        $this->assertEquals(array('block_type_group' => $this->blockTypeGroup), $this->registry->getBlockTypeGroups());
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::hasBlockTypeGroup
     */
    public function testHasBlockTypeGroup()
    {
        $this->assertTrue($this->registry->hasBlockTypeGroup('block_type_group'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::hasBlockTypeGroup
     */
    public function testHasBlockTypeWithNoBlockTypeGroup()
    {
        $this->assertFalse($this->registry->hasBlockTypeGroup('other_block_type_group'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::getBlockTypeGroup
     */
    public function testGetBlockTypeGroup()
    {
        $this->assertEquals($this->blockTypeGroup, $this->registry->getBlockTypeGroup('block_type_group'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::getBlockTypeGroup
     * @expectedException \Netgen\BlockManager\Exception\Block\BlockTypeException
     * @expectedExceptionMessage Block type group with "other_block_type_group" identifier does not exist.
     */
    public function testGetBlockTypeGroupThrowsBlockTypeException()
    {
        $this->registry->getBlockTypeGroup('other_block_type_group');
    }
}
