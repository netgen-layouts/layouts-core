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
                'type' => Item::TYPE_OVERRIDE,
                'value' => 32,
                'valueType' => 'my_value_type',
                'status' => Value::STATUS_PUBLISHED,
            ]
        );

        $this->assertSame(42, $item->id);
        $this->assertSame(30, $item->collectionId);
        $this->assertSame(3, $item->position);
        $this->assertSame(Item::TYPE_OVERRIDE, $item->type);
        $this->assertSame(32, $item->value);
        $this->assertSame('my_value_type', $item->valueType);
        $this->assertSame(Value::STATUS_PUBLISHED, $item->status);
    }
}
