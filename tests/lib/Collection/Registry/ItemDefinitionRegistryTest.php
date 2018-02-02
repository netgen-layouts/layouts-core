<?php

namespace Netgen\BlockManager\Tests\Collection\Registry;

use ArrayIterator;
use Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry;
use Netgen\BlockManager\Tests\Collection\Stubs\ItemDefinition;
use PHPUnit\Framework\TestCase;

final class ItemDefinitionRegistryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Collection\Item\ItemDefinitionInterface
     */
    private $itemDefinition;

    /**
     * @var \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry
     */
    private $registry;

    public function setUp()
    {
        $this->registry = new ItemDefinitionRegistry();

        $this->itemDefinition = new ItemDefinition('item_definition');

        $this->registry->addItemDefinition('item_definition', $this->itemDefinition);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry::addItemDefinition
     * @covers \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry::getItemDefinitions
     */
    public function testAddItemDefinition()
    {
        $this->assertEquals(array('item_definition' => $this->itemDefinition), $this->registry->getItemDefinitions());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry::getItemDefinition
     */
    public function testGetItemDefinition()
    {
        $this->assertEquals($this->itemDefinition, $this->registry->getItemDefinition('item_definition'));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry::getItemDefinition
     * @expectedException \Netgen\BlockManager\Exception\Collection\ItemDefinitionException
     * @expectedExceptionMessage Item definition for "other_item_definition" value type does not exist.
     */
    public function testGetItemDefinitionThrowsItemDefinitionException()
    {
        $this->registry->getItemDefinition('other_item_definition');
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry::hasItemDefinition
     */
    public function testHasItemDefinition()
    {
        $this->assertTrue($this->registry->hasItemDefinition('item_definition'));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry::hasItemDefinition
     */
    public function testHasItemDefinitionWithNoItemDefinition()
    {
        $this->assertFalse($this->registry->hasItemDefinition('other_item_definition'));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry::getIterator
     */
    public function testGetIterator()
    {
        $this->assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());

        $itemDefinitions = array();
        foreach ($this->registry as $identifier => $itemDefinition) {
            $itemDefinitions[$identifier] = $itemDefinition;
        }

        $this->assertEquals($this->registry->getItemDefinitions(), $itemDefinitions);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry::count
     */
    public function testCount()
    {
        $this->assertCount(1, $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry::offsetExists
     */
    public function testOffsetExists()
    {
        $this->assertArrayHasKey('item_definition', $this->registry);
        $this->assertArrayNotHasKey('other', $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry::offsetGet
     */
    public function testOffsetGet()
    {
        $this->assertEquals($this->itemDefinition, $this->registry['item_definition']);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry::offsetSet
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Method call not supported.
     */
    public function testOffsetSet()
    {
        $this->registry['item_definition'] = $this->itemDefinition;
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry::offsetUnset
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Method call not supported.
     */
    public function testOffsetUnset()
    {
        unset($this->registry['item_definition']);
    }
}
