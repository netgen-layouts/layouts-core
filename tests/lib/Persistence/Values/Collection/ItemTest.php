<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Persistence\Values\Collection;

use Netgen\BlockManager\Persistence\Values\Collection\Item;
use Netgen\BlockManager\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

final class ItemTest extends TestCase
{
    public function testSetProperties(): void
    {
        $item = Item::fromArray(
            [
                'id' => 42,
                'collectionId' => 30,
                'position' => 3,
                'value' => 32,
                'valueType' => 'my_value_type',
                'status' => Value::STATUS_PUBLISHED,
            ]
        );

        self::assertSame(42, $item->id);
        self::assertSame(30, $item->collectionId);
        self::assertSame(3, $item->position);
        self::assertSame(32, $item->value);
        self::assertSame('my_value_type', $item->valueType);
        self::assertSame(Value::STATUS_PUBLISHED, $item->status);
    }
}
