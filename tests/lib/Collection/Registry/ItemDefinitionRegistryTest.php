<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Registry;

use ArrayIterator;
use Netgen\Layouts\Collection\Item\ItemDefinition;
use Netgen\Layouts\Collection\Registry\ItemDefinitionRegistry;
use Netgen\Layouts\Exception\Collection\ItemDefinitionException;
use Netgen\Layouts\Exception\RuntimeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ItemDefinitionRegistry::class)]
final class ItemDefinitionRegistryTest extends TestCase
{
    private ItemDefinition $itemDefinition;

    private ItemDefinitionRegistry $registry;

    protected function setUp(): void
    {
        $this->itemDefinition = ItemDefinition::fromArray(['valueType' => 'item_definition']);

        $this->registry = new ItemDefinitionRegistry(['item_definition' => $this->itemDefinition]);
    }

    public function testGetItemDefinitions(): void
    {
        self::assertSame(['item_definition' => $this->itemDefinition], $this->registry->getItemDefinitions());
    }

    public function testGetItemDefinition(): void
    {
        self::assertSame($this->itemDefinition, $this->registry->getItemDefinition('item_definition'));
    }

    public function testGetItemDefinitionThrowsItemDefinitionException(): void
    {
        $this->expectException(ItemDefinitionException::class);
        $this->expectExceptionMessage('Item definition for "other_item_definition" value type does not exist.');

        $this->registry->getItemDefinition('other_item_definition');
    }

    public function testHasItemDefinition(): void
    {
        self::assertTrue($this->registry->hasItemDefinition('item_definition'));
    }

    public function testHasItemDefinitionWithNoItemDefinition(): void
    {
        self::assertFalse($this->registry->hasItemDefinition('other_item_definition'));
    }

    public function testGetIterator(): void
    {
        self::assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());

        $itemDefinitions = [];
        foreach ($this->registry as $identifier => $itemDefinition) {
            $itemDefinitions[$identifier] = $itemDefinition;
        }

        self::assertSame($this->registry->getItemDefinitions(), $itemDefinitions);
    }

    public function testCount(): void
    {
        self::assertCount(1, $this->registry);
    }

    public function testOffsetExists(): void
    {
        self::assertArrayHasKey('item_definition', $this->registry);
        self::assertArrayNotHasKey('other', $this->registry);
    }

    public function testOffsetGet(): void
    {
        self::assertSame($this->itemDefinition, $this->registry['item_definition']);
    }

    public function testOffsetSet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $this->registry['item_definition'] = $this->itemDefinition;
    }

    public function testOffsetUnset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        unset($this->registry['item_definition']);
    }
}
