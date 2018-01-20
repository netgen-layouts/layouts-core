<?php

namespace Netgen\BlockManager\Tests\Block\Registry;

use ArrayIterator;
use Netgen\BlockManager\Block\BlockType\BlockTypeGroup;
use Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry;
use PHPUnit\Framework\TestCase;

final class BlockTypeGroupRegistryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockType\BlockTypeGroup
     */
    private $blockTypeGroup;

    /**
     * @var \Netgen\BlockManager\Block\BlockType\BlockTypeGroup
     */
    private $blockTypeGroup2;

    /**
     * @var \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry
     */
    private $registry;

    public function setUp()
    {
        $this->registry = new BlockTypeGroupRegistry();

        $this->blockTypeGroup = new BlockTypeGroup(array('isEnabled' => true, 'identifier' => 'block_type_group'));
        $this->blockTypeGroup2 = new BlockTypeGroup(array('isEnabled' => false, 'identifier' => 'block_type_group2'));

        $this->registry->addBlockTypeGroup('block_type_group', $this->blockTypeGroup);
        $this->registry->addBlockTypeGroup('block_type_group2', $this->blockTypeGroup2);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::addBlockTypeGroup
     */
    public function testAddBlockTypeGroup()
    {
        $this->registry->addBlockTypeGroup('test', $this->blockTypeGroup);

        $this->assertTrue($this->registry->hasBlockTypeGroup('test'));
        $this->assertEquals($this->blockTypeGroup, $this->registry->getBlockTypeGroup('test'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::getBlockTypeGroups
     */
    public function testGetBlockTypeGroups()
    {
        $this->assertEquals(
            array(
                'block_type_group' => $this->blockTypeGroup,
                'block_type_group2' => $this->blockTypeGroup2,
            ),
            $this->registry->getBlockTypeGroups()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::getBlockTypeGroups
     */
    public function testGetEnabledBlockTypeGroups()
    {
        $this->assertEquals(
            array(
                'block_type_group' => $this->blockTypeGroup,
            ),
            $this->registry->getBlockTypeGroups(true)
        );
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

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::getIterator
     */
    public function testGetIterator()
    {
        $this->assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());

        $blockTypeGroups = array();
        foreach ($this->registry as $identifier => $blockTypeGroup) {
            $blockTypeGroups[$identifier] = $blockTypeGroup;
        }

        $this->assertEquals($this->registry->getBlockTypeGroups(), $blockTypeGroups);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::count
     */
    public function testCount()
    {
        $this->assertCount(2, $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::offsetExists
     */
    public function testOffsetExists()
    {
        $this->assertArrayHasKey('block_type_group', $this->registry);
        $this->assertArrayNotHasKey('other', $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::offsetGet
     */
    public function testOffsetGet()
    {
        $this->assertEquals($this->blockTypeGroup, $this->registry['block_type_group']);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::offsetSet
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Method call not supported.
     */
    public function testOffsetSet()
    {
        $this->registry['block_type_group'] = $this->blockTypeGroup;
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistry::offsetUnset
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Method call not supported.
     */
    public function testOffsetUnset()
    {
        unset($this->registry['block_type_group']);
    }
}
