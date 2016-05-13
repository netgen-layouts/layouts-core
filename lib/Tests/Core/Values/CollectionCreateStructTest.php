<?php

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\API\Values\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\Collection\Collection;

class CollectionCreateStructTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultProperties()
    {
        $collectionCreateStruct = new CollectionCreateStruct();

        self::assertNull($collectionCreateStruct->name);
        self::assertEquals(Collection::STATUS_DRAFT, $collectionCreateStruct->status);
    }

    public function testSetProperties()
    {
        $collectionCreateStruct = new CollectionCreateStruct(
            array(
                'name' => 'My collection',
                'status' => Collection::STATUS_PUBLISHED,
            )
        );

        self::assertEquals('My collection', $collectionCreateStruct->name);
        self::assertEquals(Collection::STATUS_PUBLISHED, $collectionCreateStruct->status);
    }
}
