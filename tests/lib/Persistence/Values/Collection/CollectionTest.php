<?php

namespace Netgen\BlockManager\Tests\Persistence\Values\Collection;

use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    public function testSetDefaultProperties()
    {
        $collection = new Collection();

        $this->assertNull($collection->id);
        $this->assertNull($collection->type);
        $this->assertNull($collection->shared);
        $this->assertNull($collection->name);
        $this->assertNull($collection->status);
    }

    public function testSetProperties()
    {
        $collection = new Collection(
            array(
                'id' => 42,
                'type' => Collection::TYPE_DYNAMIC,
                'shared' => false,
                'name' => 'My collection',
                'status' => Value::STATUS_PUBLISHED,
            )
        );

        $this->assertEquals(42, $collection->id);
        $this->assertEquals(Collection::TYPE_DYNAMIC, $collection->type);
        $this->assertFalse($collection->shared);
        $this->assertEquals('My collection', $collection->name);
        $this->assertEquals(Value::STATUS_PUBLISHED, $collection->status);
    }
}
