<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Collection;

use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\API\Values\Collection\ItemList;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\API\Values\Collection\Slot;
use Netgen\Layouts\API\Values\Collection\SlotList;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

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

        $uuid = Uuid::v4();
        $blockUuid = Uuid::v4();

        $collection = Collection::fromArray(
            [
                'id' => $uuid,
                'blockId' => $blockUuid,
                'offset' => 5,
                'limit' => 10,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'isTranslatable' => true,
                'isAlwaysAvailable' => false,
                'locale' => 'en',
                'items' => ItemList::fromArray($items),
                'slots' => SlotList::fromArray($slots),
                'query' => $query,
            ],
        );

        self::assertSame($uuid->toString(), $collection->id->toString());
        self::assertSame($blockUuid->toString(), $collection->blockId->toString());
        self::assertSame(5, $collection->offset);
        self::assertSame(10, $collection->limit);
        self::assertSame('en', $collection->mainLocale);
        self::assertSame(['en', 'hr'], $collection->availableLocales);
        self::assertTrue($collection->isTranslatable);
        self::assertFalse($collection->isAlwaysAvailable);
        self::assertSame('en', $collection->locale);

        self::assertCount(2, $collection->items);
        self::assertSame($items[0], $collection->items[0]);
        self::assertSame($items[1], $collection->items[1]);

        self::assertCount(2, $collection->slots);
        self::assertSame($slots[2], $collection->slots[2]);
        self::assertSame($slots[3], $collection->slots[3]);

        self::assertSame($query, $collection->query);
        self::assertTrue($collection->hasQuery);

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
                'id' => Uuid::v4(),
                'offset' => 5,
                'query' => null,
            ],
        );

        self::assertSame(0, $collection->offset);
    }

    public function testGetItemWithNonExistingPosition(): void
    {
        $collection = Collection::fromArray(
            [
                'id' => Uuid::v4(),
                'items' => ItemList::fromArray([Item::fromArray(['position' => 0])]),
            ],
        );

        self::assertNull($collection->getItem(999));
    }
}
