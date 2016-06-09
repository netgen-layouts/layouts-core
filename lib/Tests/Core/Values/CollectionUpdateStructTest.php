<?php

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\API\Values\CollectionUpdateStruct;
use PHPUnit\Framework\TestCase;

class CollectionUpdateStructTest extends TestCase
{
    public function testDefaultProperties()
    {
        $collectionUpdateStruct = new CollectionUpdateStruct();

        self::assertNull($collectionUpdateStruct->name);
    }

    public function testSetProperties()
    {
        $collectionUpdateStruct = new CollectionUpdateStruct(
            array(
                'name' => 'My collection',
            )
        );

        self::assertEquals('My collection', $collectionUpdateStruct->name);
    }
}
