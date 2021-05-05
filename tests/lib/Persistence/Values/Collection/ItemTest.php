<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Values\Collection;

use Netgen\Layouts\Persistence\Values\Collection\Item;
use Netgen\Layouts\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

final class ItemTest extends TestCase
{
    /**
     * @coversNothing
     */
    public function testSetProperties(): void
    {
        $item = Item::fromArray(
            [
                'id' => 42,
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'collectionId' => 30,
                'collectionUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'position' => 3,
                'value' => 32,
                'valueType' => 'my_value_type',
                'viewType' => 'my_view_type',
                'config' => ['key' => ['param' => 'value']],
                'status' => Value::STATUS_PUBLISHED,
            ],
        );

        self::assertSame(42, $item->id);
        self::assertSame('4adf0f00-f6c2-5297-9f96-039bfabe8d3b', $item->uuid);
        self::assertSame(30, $item->collectionId);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $item->collectionUuid);
        self::assertSame(3, $item->position);
        self::assertSame(32, $item->value);
        self::assertSame('my_value_type', $item->valueType);
        self::assertSame('my_view_type', $item->viewType);
        self::assertSame(['key' => ['param' => 'value']], $item->config);
        self::assertSame(Value::STATUS_PUBLISHED, $item->status);
    }
}
