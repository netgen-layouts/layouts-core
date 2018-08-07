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
        $this->itemDefinition = ItemDefinition::fromArray(['valueType' => 'item_definition']);

        $this->registry = new ItemDefinitionRegistry(['item_definition' => $this->itemDefinition]);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry::__construct
     * @covers \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry::getItemDefinitions
     */
    public function testGetItemDefinitions(): void
    {
        self::assertSame(['item_definition' => $this->itemDefinition], $this->registry->getItemDefinitions());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry::getItemDefinition
     */
    public function testGetItemDefinition(): void
    {
        self::assertSame($this->itemDefinition, $this->registry->getItemDefinition('item_definition'));
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
        self::assertTrue($this->registry->hasItemDefinition('item_definition'));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry::hasItemDefinition
     */
    public function testHasItemDefinitionWithNoItemDefinition(): void
    {
        self::assertFalse($this->registry->hasItemDefinition('other_item_definition'));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry::getIterator
     */
    public function testGetIterator(): void
    {
        self::assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());

        $itemDefinitions = [];
        foreach ($this->registry as $identifier => $itemDefinition) {
            $itemDefinitions[$identifier] = $itemDefinition;
        }

        self::assertSame($this->registry->getItemDefinitions(), $itemDefinitions);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry::count
     */
    public function testCount(): void
    {
        self::assertCount(1, $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry::offsetExists
     */
    public function testOffsetExists(): void
    {
        self::assertArrayHasKey('item_definition', $this->registry);
        self::assertArrayNotHasKey('other', $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry::offsetGet
     */
    public function testOffsetGet(): void
    {
        self::assertSame($this->itemDefinition, $this->registry['item_definition']);
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
