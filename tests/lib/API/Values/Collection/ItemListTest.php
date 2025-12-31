<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Collection;

use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\API\Values\Collection\ItemList;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(ItemList::class)]
final class ItemListTest extends TestCase
{
    public function testGetItems(): void
    {
        $items = [new Item(), new Item()];

        self::assertSame($items, ItemList::fromArray($items)->getItems());
    }

    public function testGetItemIds(): void
    {
        $uuid1 = Uuid::v7();
        $uuid2 = Uuid::v7();

        $items = [Item::fromArray(['id' => $uuid1]), Item::fromArray(['id' => $uuid2])];

        self::assertSame([$uuid1, $uuid2], ItemList::fromArray($items)->getItemIds());
    }
}
