<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Values\Collection;

use Netgen\Layouts\Persistence\Values\Collection\Item;
use Netgen\Layouts\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

final class ItemTest extends TestCase
{
    public function testSetProperties(): void
    {
        $item = Item::fromArray(
            [
                'id' => 42,
                'collectionId' => 30,
                'collectionUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'position' => 3,
                'value' => 32,
                'valueType' => 'my_value_type',
                'config' => ['param' => ['value']],
                'status' => Value::STATUS_PUBLISHED,
            ]
        );

        self::assertSame(42, $item->id);
        self::assertSame(30, $item->collectionId);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $item->collectionUuid);
        self::assertSame(3, $item->position);
        self::assertSame(32, $item->value);
        self::assertSame('my_value_type', $item->valueType);
        self::assertSame(['param' => ['value']], $item->config);
        self::assertSame(Value::STATUS_PUBLISHED, $item->status);
    }
}
