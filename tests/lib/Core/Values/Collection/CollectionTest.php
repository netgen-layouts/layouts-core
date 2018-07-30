<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Core\Values\Collection\Query;
use PHPUnit\Framework\TestCase;

final class CollectionTest extends TestCase
{
    public function testInstance(): void
    {
        $this->assertInstanceOf(Value::class, new Collection());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::__construct
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getAvailableLocales
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getItems
     */
    public function testDefaultProperties(): void
    {
        $collection = new Collection();

        $this->assertSame([], $collection->getAvailableLocales());
        $this->assertSame([], $collection->getItems());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::__construct
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::filterItems
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getAvailableLocales
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getItem
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getItems
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getLimit
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getLocale
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getMainLocale
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getManualItem
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getManualItems
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getOffset
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getOverrideItem
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getOverrideItems
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getQuery
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::hasItem
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::hasManualItem
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::hasOverrideItem
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::hasQuery
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::isAlwaysAvailable
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::isTranslatable
     */
    public function testSetProperties(): void
    {
        $items = [
            Item::fromArray(['type' => Item::TYPE_MANUAL, 'position' => 3]),
            Item::fromArray(['type' => Item::TYPE_OVERRIDE, 'position' => 5]),
        ];

        $query = new Query();

        $collection = Collection::fromArray(
            [
                'id' => 42,
                'offset' => 5,
                'limit' => 10,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'isTranslatable' => true,
                'alwaysAvailable' => false,
                'locale' => 'en',
                'items' => new ArrayCollection($items),
                'query' => $query,
            ]
        );

        $this->assertSame(42, $collection->getId());
        $this->assertSame(5, $collection->getOffset());
        $this->assertSame(10, $collection->getLimit());
        $this->assertSame('en', $collection->getMainLocale());
        $this->assertSame(['en', 'hr'], $collection->getAvailableLocales());
        $this->assertTrue($collection->isTranslatable());
        $this->assertFalse($collection->isAlwaysAvailable());
        $this->assertSame('en', $collection->getLocale());
        $this->assertCount(2, $collection->getItems());
        $this->assertCount(1, $collection->getManualItems());
        $this->assertCount(1, $collection->getOverrideItems());

        $this->assertInstanceOf(Item::class, $collection->getItem(3));
        $this->assertInstanceOf(Item::class, $collection->getItem(5));
        $this->assertInstanceOf(Item::class, $collection->getManualItem(3));
        $this->assertInstanceOf(Item::class, $collection->getOverrideItem(5));

        $this->assertSame(Item::TYPE_MANUAL, $collection->getItem(3)->getType());
        $this->assertSame(Item::TYPE_OVERRIDE, $collection->getItem(5)->getType());
        $this->assertSame(Item::TYPE_MANUAL, $collection->getManualItem(3)->getType());
        $this->assertSame(Item::TYPE_OVERRIDE, $collection->getOverrideItem(5)->getType());

        $this->assertSame($query, $collection->getQuery());
        $this->assertTrue($collection->hasQuery());

        $this->assertFalse($collection->hasItem(2));
        $this->assertTrue($collection->hasItem(3));
        $this->assertTrue($collection->hasItem(5));

        $this->assertSame($items[0], $collection->getItem(3));
        $this->assertSame($items[1], $collection->getItem(5));

        $this->assertFalse($collection->hasManualItem(2));
        $this->assertTrue($collection->hasManualItem(3));

        $this->assertSame($items[0], $collection->getManualItem(3));
        $this->assertNull($collection->getManualItem(2));

        $this->assertFalse($collection->hasOverrideItem(4));
        $this->assertTrue($collection->hasOverrideItem(5));

        $this->assertSame($items[1], $collection->getOverrideItem(5));
        $this->assertNull($collection->getOverrideItem(4));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getOffset
     */
    public function testGetOffsetForManualCollection(): void
    {
        $collection = Collection::fromArray(
            [
                'offset' => 5,
            ]
        );

        $this->assertSame(0, $collection->getOffset());
    }
}
