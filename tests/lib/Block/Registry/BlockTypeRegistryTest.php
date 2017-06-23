<?php

namespace Netgen\BlockManager\Tests\Block\Registry;

use ArrayIterator;
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
     * @var \Netgen\BlockManager\Block\BlockType\BlockType
     */
    protected $blockType2;

    /**
     * @var \Netgen\BlockManager\Block\Registry\BlockTypeRegistry
     */
    protected $registry;

    public function setUp()
    {
        $this->registry = new BlockTypeRegistry();

        $this->blockType = new BlockType(array('isEnabled' => true, 'identifier' => 'block_type'));
        $this->blockType2 = new BlockType(array('isEnabled' => false, 'identifier' => 'block_type2'));

        $this->registry->addBlockType('block_type', $this->blockType);
        $this->registry->addBlockType('block_type2', $this->blockType2);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeRegistry::addBlockType
     */
    public function testAddBlockType()
    {
        $this->registry->addBlockType('test', $this->blockType);

        $this->assertTrue($this->registry->hasBlockType('test'));
        $this->assertEquals($this->blockType, $this->registry->getBlockType('test'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeRegistry::getBlockTypes
     */
    public function testGetBlockTypes()
    {
        $this->assertEquals(
            array(
                'block_type' => $this->blockType,
                'block_type2' => $this->blockType2,
            ),
            $this->registry->getBlockTypes()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeRegistry::getBlockTypes
     */
    public function testGetEnabledBlockTypes()
    {
        $this->assertEquals(
            array(
                'block_type' => $this->blockType,
            ),
            $this->registry->getBlockTypes(true)
        );
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
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeRegistry::getIterator
     */
    public function testGetIterator()
    {
        $this->assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());

        $blockTypes = array();
        foreach ($this->registry as $identifier => $blockType) {
            $blockTypes[$identifier] = $blockType;
        }

        $this->assertEquals($this->registry->getBlockTypes(), $blockTypes);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeRegistry::count
     */
    public function testCount()
    {
        $this->assertCount(2, $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeRegistry::offsetExists
     */
    public function testOffsetExists()
    {
        $this->assertArrayHasKey('block_type', $this->registry);
        $this->assertArrayNotHasKey('other', $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeRegistry::offsetGet
     */
    public function testOffsetGet()
    {
        $this->assertEquals($this->blockType, $this->registry['block_type']);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeRegistry::offsetSet
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Method call not supported.
     */
    public function testOffsetSet()
    {
        $this->registry['block_type'] = $this->blockType;
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeRegistry::offsetUnset
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Method call not supported.
     */
    public function testOffsetUnset()
    {
        unset($this->registry['block_type']);
    }
}
