<?php

namespace Netgen\BlockManager\Tests\Persistence\Values\Collection;

use Netgen\BlockManager\Persistence\Values\Value;
use Netgen\BlockManager\Persistence\Values\Collection\Item;
use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    public function testSetDefaultProperties()
    {
        $item = new Item();

        $this->assertNull($item->id);
        $this->assertNull($item->collectionId);
        $this->assertNull($item->position);
        $this->assertNull($item->type);
        $this->assertNull($item->valueId);
        $this->assertNull($item->valueType);
        $this->assertNull($item->status);
    }

    public function testSetProperties()
    {
        $item = new Item(
            array(
                'id' => 42,
                'collectionId' => 30,
                'position' => 3,
                'type' => Item::TYPE_OVERRIDE,
                'valueId' => 32,
                'valueType' => 'ezcontent',
                'status' => Value::STATUS_PUBLISHED,
            )
        );

        $this->assertEquals(42, $item->id);
        $this->assertEquals(30, $item->collectionId);
        $this->assertEquals(3, $item->position);
        $this->assertEquals(Item::TYPE_OVERRIDE, $item->type);
        $this->assertEquals(32, $item->valueId);
        $this->assertEquals('ezcontent', $item->valueType);
        $this->assertEquals(Value::STATUS_PUBLISHED, $item->status);
    }
}
