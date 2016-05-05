<?php

namespace Netgen\BlockManager\Tests\Persistence\Values\Collection;

use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\Collection as APICollection;

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
                'type' => APICollection::TYPE_NAMED,
                'name' => 'My collection',
                'status' => APICollection::STATUS_PUBLISHED,
            )
        );

        self::assertEquals(42, $collection->id);
        self::assertEquals(APICollection::TYPE_NAMED, $collection->type);
        self::assertEquals('My collection', $collection->name);
        self::assertEquals(APICollection::STATUS_PUBLISHED, $collection->status);
    }
}
