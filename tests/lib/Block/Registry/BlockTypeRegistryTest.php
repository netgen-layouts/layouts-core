<?php

namespace Netgen\BlockManager\Tests\Block\Registry;

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
     * @var \Netgen\BlockManager\Block\Registry\BlockTypeRegistry
     */
    protected $registry;

    public function setUp()
    {
        $this->registry = new BlockTypeRegistry();

        $this->blockType = new BlockType(array('identifier' => 'block_type'));

        $this->registry->addBlockType('block_type', $this->blockType);
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
}
