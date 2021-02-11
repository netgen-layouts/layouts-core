<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Registry;

use ArrayIterator;
use Netgen\Layouts\Collection\Item\ItemDefinition;
use Netgen\Layouts\Collection\Registry\ItemDefinitionRegistry;
use Netgen\Layouts\Exception\Collection\ItemDefinitionException;
use Netgen\Layouts\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;

final class ItemDefinitionRegistryTest extends TestCase
{
    private ItemDefinition $itemDefinition;

    private ItemDefinitionRegistry $registry;

    protected function setUp(): void
    {
        $this->itemDefinition = ItemDefinition::fromArray(['valueType' => 'item_definition']);

        $this->registry = new ItemDefinitionRegistry(['item_definition' => $this->itemDefinition]);
    }

    /**
     * @covers \Netgen\Layouts\Collection\Registry\ItemDefinitionRegistry::__construct
     * @covers \Netgen\Layouts\Collection\Registry\ItemDefinitionRegistry::getItemDefinitions
     */
    public function testGetItemDefinitions(): void
    {
        self::assertSame(['item_definition' => $this->itemDefinition], $this->registry->getItemDefinitions());
    }

    /**
     * @covers \Netgen\Layouts\Collection\Registry\ItemDefinitionRegistry::getItemDefinition
     */
    public function testGetItemDefinition(): void
    {
        self::assertSame($this->itemDefinition, $this->registry->getItemDefinition('item_definition'));
    }

    /**
     * @covers \Netgen\Layouts\Collection\Registry\ItemDefinitionRegistry::getItemDefinition
     */
    public function testGetItemDefinitionThrowsItemDefinitionException(): void
    {
        $this->expectException(ItemDefinitionException::class);
        $this->expectExceptionMessage('Item definition for "other_item_definition" value type does not exist.');

        $this->registry->getItemDefinition('other_item_definition');
    }

    /**
     * @covers \Netgen\Layouts\Collection\Registry\ItemDefinitionRegistry::hasItemDefinition
     */
    public function testHasItemDefinition(): void
    {
        self::assertTrue($this->registry->hasItemDefinition('item_definition'));
    }

    /**
     * @covers \Netgen\Layouts\Collection\Registry\ItemDefinitionRegistry::hasItemDefinition
     */
    public function testHasItemDefinitionWithNoItemDefinition(): void
    {
        self::assertFalse($this->registry->hasItemDefinition('other_item_definition'));
    }

    /**
     * @covers \Netgen\Layouts\Collection\Registry\ItemDefinitionRegistry::getIterator
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
     * @covers \Netgen\Layouts\Collection\Registry\ItemDefinitionRegistry::count
     */
    public function testCount(): void
    {
        self::assertCount(1, $this->registry);
    }

    /**
     * @covers \Netgen\Layouts\Collection\Registry\ItemDefinitionRegistry::offsetExists
     */
    public function testOffsetExists(): void
    {
        self::assertArrayHasKey('item_definition', $this->registry);
        self::assertArrayNotHasKey('other', $this->registry);
    }

    /**
     * @covers \Netgen\Layouts\Collection\Registry\ItemDefinitionRegistry::offsetGet
     */
    public function testOffsetGet(): void
    {
        self::assertSame($this->itemDefinition, $this->registry['item_definition']);
    }

    /**
     * @covers \Netgen\Layouts\Collection\Registry\ItemDefinitionRegistry::offsetSet
     */
    public function testOffsetSet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $this->registry['item_definition'] = $this->itemDefinition;
    }

    /**
     * @covers \Netgen\Layouts\Collection\Registry\ItemDefinitionRegistry::offsetUnset
     */
    public function testOffsetUnset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        unset($this->registry['item_definition']);
    }
}
