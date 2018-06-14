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
        $item = new Item(
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

        $this->assertEquals(42, $item->id);
        $this->assertEquals(30, $item->collectionId);
        $this->assertEquals(3, $item->position);
        $this->assertEquals(Item::TYPE_OVERRIDE, $item->type);
        $this->assertEquals(32, $item->value);
        $this->assertEquals('my_value_type', $item->valueType);
        $this->assertEquals(Value::STATUS_PUBLISHED, $item->status);
    }
}
