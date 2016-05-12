<?php

namespace Netgen\BlockManager\Tests\Persistence\Values\Collection;

use Netgen\BlockManager\Persistence\Values\Collection\Collection;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testSetDefaultProperties()
    {
        $collection = new Collection();

        self::assertNull($collection->id);
        self::assertNull($collection->type);
        self::assertNull($collection->name);
        self::assertNull($collection->status);
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

        self::assertEquals(42, $collection->id);
        self::assertEquals(Collection::TYPE_NAMED, $collection->type);
        self::assertEquals('My collection', $collection->name);
        self::assertEquals(Collection::STATUS_PUBLISHED, $collection->status);
    }
}
