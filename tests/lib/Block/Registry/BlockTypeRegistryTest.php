<?php

namespace Netgen\BlockManager\Tests\Block\Registry;

use Netgen\BlockManager\Block\BlockType\BlockTypeGroup;
use Netgen\BlockManager\Block\Registry\BlockTypeRegistry;
use Netgen\BlockManager\Tests\Block\Stubs\BlockType;
use PHPUnit\Framework\TestCase;

class BlockTypeRegistryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockType\BlockType
     */
    protected $blockType;

    /**
     * @var \Netgen\BlockManager\Block\BlockType\BlockTypeGroup
     */
    protected $blockTypeGroup;

    /**
     * @var \Netgen\BlockManager\Block\Registry\BlockTypeRegistry
     */
    protected $registry;

    public function setUp()
    {
        $this->registry = new BlockTypeRegistry();

        $this->blockType = new BlockType(array('identifier' => 'block_type'));

        $this->blockTypeGroup = new BlockTypeGroup(array('identifier' => 'block_type_group'));

        $this->registry->addBlockType('block_type', $this->blockType);
        $this->registry->addBlockTypeGroup('block_type_group', $this->blockTypeGroup);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeRegistry::addBlockType
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeRegistry::getBlockTypes
     */
    public function testAddBlockType()
    {
        $this->assertEquals(array('block_type' => $this->blockType), $this->registry->getBlockTypes());
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeRegistry::hasBlockType
     */
    public function testHasBlockType()
    {
        $this->assertTrue($this->registry->hasBlockType('block_type'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeRegistry::hasBlockType
     */
    public function testHasBlockTypeWithNoBlockType()
    {
        $this->assertFalse($this->registry->hasBlockType('other_block_type'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeRegistry::getBlockType
     */
    public function testGetBlockType()
    {
        $this->assertEquals($this->blockType, $this->registry->getBlockType('block_type'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeRegistry::getBlockType
     * @expectedException \Netgen\BlockManager\Exception\Block\BlockTypeException
     * @expectedExceptionMessage Block type with "other_block_type" identifier does not exist.
     */
    public function testGetBlockTypeThrowsBlockTypeException()
    {
        $this->registry->getBlockType('other_block_type');
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeRegistry::addBlockTypeGroup
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeRegistry::getBlockTypeGroups
     */
    public function testAddBlockTypeGroup()
    {
        $this->assertEquals(array('block_type_group' => $this->blockTypeGroup), $this->registry->getBlockTypeGroups());
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeRegistry::hasBlockTypeGroup
     */
    public function testHasBlockTypeGroup()
    {
        $this->assertTrue($this->registry->hasBlockTypeGroup('block_type_group'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeRegistry::hasBlockTypeGroup
     */
    public function testHasBlockTypeWithNoBlockTypeGroup()
    {
        $this->assertFalse($this->registry->hasBlockTypeGroup('other_block_type_group'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeRegistry::getBlockTypeGroup
     */
    public function testGetBlockTypeGroup()
    {
        $this->assertEquals($this->blockTypeGroup, $this->registry->getBlockTypeGroup('block_type_group'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeRegistry::getBlockTypeGroup
     * @expectedException \Netgen\BlockManager\Exception\Block\BlockTypeException
     * @expectedExceptionMessage Block type group with "other_block_type_group" identifier does not exist.
     */
    public function testGetBlockTypeGroupThrowsBlockTypeException()
    {
        $this->registry->getBlockTypeGroup('other_block_type_group');
    }
}
