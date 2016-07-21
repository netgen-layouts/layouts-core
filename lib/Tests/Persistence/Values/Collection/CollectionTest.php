<?php

namespace Netgen\BlockManager\Tests\Persistence\Values\Collection;

use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    public function testSetDefaultProperties()
    {
        $collection = new Collection();

        $this->assertNull($collection->id);
        $this->assertNull($collection->type);
        $this->assertNull($collection->name);
        $this->assertNull($collection->status);
    }

    public function testSetProperties()
    {
        $collection = new Collection(
            array(
                'id' => 42,
                'type' => Collection::TYPE_NAMED,
                'name' => 'My collection',
                'status' => Collection::STATUS_PUBLISHED,
            )
        );

        $this->assertEquals(42, $collection->id);
        $this->assertEquals(Collection::TYPE_NAMED, $collection->type);
        $this->assertEquals('My collection', $collection->name);
        $this->assertEquals(Collection::STATUS_PUBLISHED, $collection->status);
    }
}
