<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Registry;

use ArrayIterator;
use Netgen\BlockManager\Collection\Item\ItemDefinition;
use Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry;
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

    public function setUp(): void
    {
        $this->registry = new ItemDefinitionRegistry();

        $this->itemDefinition = new ItemDefinition(['valueType' => 'item_definition']);

        $this->registry->addItemDefinition('item_definition', $this->itemDefinition);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry::addItemDefinition
     * @covers \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry::getItemDefinitions
     */
    public function testAddItemDefinition(): void
    {
        $this->assertSame(['item_definition' => $this->itemDefinition], $this->registry->getItemDefinitions());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry::getItemDefinition
     */
    public function testGetItemDefinition(): void
    {
        $this->assertSame($this->itemDefinition, $this->registry->getItemDefinition('item_definition'));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry::getItemDefinition
     * @expectedException \Netgen\BlockManager\Exception\Collection\ItemDefinitionException
     * @expectedExceptionMessage Item definition for "other_item_definition" value type does not exist.
     */
    public function testGetItemDefinitionThrowsItemDefinitionException(): void
    {
        $this->registry->getItemDefinition('other_item_definition');
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry::hasItemDefinition
     */
    public function testHasItemDefinition(): void
    {
        $this->assertTrue($this->registry->hasItemDefinition('item_definition'));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry::hasItemDefinition
     */
    public function testHasItemDefinitionWithNoItemDefinition(): void
    {
        $this->assertFalse($this->registry->hasItemDefinition('other_item_definition'));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry::getIterator
     */
    public function testGetIterator(): void
    {
        $this->assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());

        $itemDefinitions = [];
        foreach ($this->registry as $identifier => $itemDefinition) {
            $itemDefinitions[$identifier] = $itemDefinition;
        }

        $this->assertSame($this->registry->getItemDefinitions(), $itemDefinitions);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry::count
     */
    public function testCount(): void
    {
        $this->assertCount(1, $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry::offsetExists
     */
    public function testOffsetExists(): void
    {
        $this->assertArrayHasKey('item_definition', $this->registry);
        $this->assertArrayNotHasKey('other', $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry::offsetGet
     */
    public function testOffsetGet(): void
    {
        $this->assertSame($this->itemDefinition, $this->registry['item_definition']);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry::offsetSet
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Method call not supported.
     */
    public function testOffsetSet(): void
    {
        $this->registry['item_definition'] = $this->itemDefinition;
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry::offsetUnset
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Method call not supported.
     */
    public function testOffsetUnset(): void
    {
        unset($this->registry['item_definition']);
    }
}
