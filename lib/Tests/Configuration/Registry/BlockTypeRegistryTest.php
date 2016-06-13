<?php

namespace Netgen\BlockManager\Tests\Configuration\Registry;

use Netgen\BlockManager\Configuration\BlockType\BlockType;
use Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup;
use Netgen\BlockManager\Configuration\Registry\BlockTypeRegistry;
use PHPUnit\Framework\TestCase;

class BlockTypeRegistryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Configuration\BlockType\BlockType
     */
    protected $blockType;

    /**
     * @var \Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup
     */
    protected $blockTypeGroup;

    /**
     * @var \Netgen\BlockManager\Configuration\Registry\BlockTypeRegistry
     */
    protected $registry;

    public function setUp()
    {
        $this->registry = new BlockTypeRegistry();

        $this->blockType = new BlockType(
            'block_type',
            true,
            'Block type',
            'paragraph',
            array()
        );

        $this->blockTypeGroup = new BlockTypeGroup(
            'block_type_group',
            true,
            'Block type group',
            array()
        );

        $this->registry->addBlockType($this->blockType);
        $this->registry->addBlockTypeGroup($this->blockTypeGroup);
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Registry\BlockTypeRegistry::addBlockType
     * @covers \Netgen\BlockManager\Configuration\Registry\BlockTypeRegistry::getBlockTypes
     */
    public function testAddBlockType()
    {
        self::assertEquals(array('block_type' => $this->blockType), $this->registry->getBlockTypes());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Registry\BlockTypeRegistry::hasBlockType
     */
    public function testHasBlockType()
    {
        self::assertTrue($this->registry->hasBlockType('block_type'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Registry\BlockTypeRegistry::hasBlockType
     */
    public function testHasBlockTypeWithNoBlockType()
    {
        self::assertFalse($this->registry->hasBlockType('other_block_type'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Registry\BlockTypeRegistry::getBlockType
     */
    public function testGetBlockType()
    {
        self::assertEquals($this->blockType, $this->registry->getBlockType('block_type'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Registry\BlockTypeRegistry::getBlockType
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     */
    public function testGetBlockTypeThrowsInvalidArgumentException()
    {
        $this->registry->getBlockType('other_block_type');
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Registry\BlockTypeRegistry::addBlockTypeGroup
     * @covers \Netgen\BlockManager\Configuration\Registry\BlockTypeRegistry::getBlockTypeGroups
     */
    public function testAddBlockTypeGroup()
    {
        self::assertEquals(array('block_type_group' => $this->blockTypeGroup), $this->registry->getBlockTypeGroups());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Registry\BlockTypeRegistry::hasBlockTypeGroup
     */
    public function testHasBlockTypeGroup()
    {
        self::assertTrue($this->registry->hasBlockTypeGroup('block_type_group'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Registry\BlockTypeRegistry::hasBlockTypeGroup
     */
    public function testHasBlockTypeWithNoBlockTypeGroup()
    {
        self::assertFalse($this->registry->hasBlockTypeGroup('other_block_type_group'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Registry\BlockTypeRegistry::getBlockTypeGroup
     */
    public function testGetBlockTypeGroup()
    {
        self::assertEquals($this->blockTypeGroup, $this->registry->getBlockTypeGroup('block_type_group'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Registry\BlockTypeRegistry::getBlockTypeGroup
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     */
    public function testGetBlockTypeGroupThrowsInvalidArgumentException()
    {
        $this->registry->getBlockTypeGroup('other_block_type_group');
    }
}
