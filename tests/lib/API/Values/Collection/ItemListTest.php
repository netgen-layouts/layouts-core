<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Collection;

use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\API\Values\Collection\ItemList;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;
use TypeError;

final class ItemListTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\API\Values\Collection\ItemList::__construct
     */
    public function testConstructorWithInvalidType(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            sprintf(
                'Argument 1 passed to %s::%s\{closure}() must be an instance of %s, instance of %s given',
                ItemList::class,
                str_replace('\ItemList', '', ItemList::class),
                Item::class,
                stdClass::class
            )
        );

        new ItemList([new Item(), new stdClass(), new Item()]);
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Collection\ItemList::__construct
     * @covers \Netgen\Layouts\API\Values\Collection\ItemList::getItems
     */
    public function testGetItems(): void
    {
        $items = [new Item(), new Item()];

        self::assertSame($items, (new ItemList($items))->getItems());
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Collection\ItemList::getItemIds
     */
    public function testGetItemIds(): void
    {
        $uuid1 = Uuid::uuid4();
        $uuid2 = Uuid::uuid4();

        $items = [Item::fromArray(['id' => $uuid1]), Item::fromArray(['id' => $uuid2])];

        self::assertSame([$uuid1, $uuid2], (new ItemList($items))->getItemIds());
    }
}
