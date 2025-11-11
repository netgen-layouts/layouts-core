<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\API\Values\Collection\ItemList;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\API\Values\Collection\Slot;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

#[CoversClass(Collection::class)]
final class CollectionTest extends TestCase
{
    public function testSetProperties(): void
    {
        $items = [
            Item::fromArray(['position' => 3]),
            Item::fromArray(['position' => 5]),
        ];

        $slots = [
            2 => Slot::fromArray(['position' => 2]),
            3 => Slot::fromArray(['position' => 3]),
        ];

        $query = new Query();

        $uuid = Uuid::uuid4();
        $blockUuid = Uuid::uuid4();

        $collection = Collection::fromArray(
            [
                'id' => $uuid,
                'blockId' => $blockUuid,
                'offset' => 5,
                'limit' => 10,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'isTranslatable' => true,
                'alwaysAvailable' => false,
                'locale' => 'en',
                'items' => new ArrayCollection($items),
                'slots' => new ArrayCollection($slots),
                'query' => $query,
            ],
        );

        self::assertSame($uuid->toString(), $collection->getId()->toString());
        self::assertSame($blockUuid->toString(), $collection->getBlockId()->toString());
        self::assertSame(5, $collection->getOffset());
        self::assertSame(10, $collection->getLimit());
        self::assertSame('en', $collection->getMainLocale());
        self::assertSame(['en', 'hr'], $collection->getAvailableLocales());
        self::assertTrue($collection->isTranslatable());
        self::assertFalse($collection->isAlwaysAvailable());
        self::assertSame('en', $collection->getLocale());

        self::assertCount(2, $collection->getItems());
        self::assertSame($items[0], $collection->getItems()[0]);
        self::assertSame($items[1], $collection->getItems()[1]);

        self::assertCount(2, $collection->getSlots());
        self::assertSame($slots[2], $collection->getSlots()[2]);
        self::assertSame($slots[3], $collection->getSlots()[3]);

        self::assertSame($query, $collection->getQuery());
        self::assertTrue($collection->hasQuery());

        self::assertFalse($collection->hasItem(2));
        self::assertTrue($collection->hasItem(3));
        self::assertTrue($collection->hasItem(5));

        self::assertSame($items[0], $collection->getItem(3));
        self::assertSame($items[1], $collection->getItem(5));

        self::assertFalse($collection->hasSlot(5));
        self::assertTrue($collection->hasSlot(2));
        self::assertTrue($collection->hasSlot(3));

        self::assertSame($slots[2], $collection->getSlot(2));
        self::assertSame($slots[3], $collection->getSlot(3));
        self::assertNull($collection->getSlot(999));
    }

    public function testGetOffsetForManualCollection(): void
    {
        $collection = Collection::fromArray(
            [
                'id' => Uuid::uuid4(),
                'offset' => 5,
            ],
        );

        self::assertSame(0, $collection->getOffset());
    }

    public function testGetItemWithNonExistingPosition(): void
    {
        $collection = Collection::fromArray(
            [
                'id' => Uuid::uuid4(),
                'items' => new ItemList([Item::fromArray(['position' => 0])]),
            ],
        );

        self::assertNull($collection->getItem(999));
    }
}
