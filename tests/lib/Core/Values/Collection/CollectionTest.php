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
        self::assertInstanceOf(Value::class, new Collection());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::__construct
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getAvailableLocales
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getItems
     */
    public function testDefaultProperties(): void
    {
        $collection = new Collection();

        self::assertSame([], $collection->getAvailableLocales());
        self::assertSame([], $collection->getItems());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::__construct
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getAvailableLocales
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getItem
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getItems
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getLimit
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getLocale
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getMainLocale
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getOffset
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::getQuery
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::hasItem
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::hasQuery
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::isAlwaysAvailable
     * @covers \Netgen\BlockManager\Core\Values\Collection\Collection::isTranslatable
     */
    public function testSetProperties(): void
    {
        $items = [
            Item::fromArray(['position' => 3]),
            Item::fromArray(['position' => 5]),
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

        self::assertSame(42, $collection->getId());
        self::assertSame(5, $collection->getOffset());
        self::assertSame(10, $collection->getLimit());
        self::assertSame('en', $collection->getMainLocale());
        self::assertSame(['en', 'hr'], $collection->getAvailableLocales());
        self::assertTrue($collection->isTranslatable());
        self::assertFalse($collection->isAlwaysAvailable());
        self::assertSame('en', $collection->getLocale());
        self::assertCount(2, $collection->getItems());

        self::assertInstanceOf(Item::class, $collection->getItem(3));
        self::assertInstanceOf(Item::class, $collection->getItem(5));

        self::assertSame($query, $collection->getQuery());
        self::assertTrue($collection->hasQuery());

        self::assertFalse($collection->hasItem(2));
        self::assertTrue($collection->hasItem(3));
        self::assertTrue($collection->hasItem(5));

        self::assertSame($items[0], $collection->getItem(3));
        self::assertSame($items[1], $collection->getItem(5));
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

        self::assertSame(0, $collection->getOffset());
    }
}
