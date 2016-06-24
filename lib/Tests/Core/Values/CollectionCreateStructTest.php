<?php

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\API\Values\CollectionCreateStruct;
use PHPUnit\Framework\TestCase;

class CollectionCreateStructTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\API\Values\CollectionCreateStruct::__construct
     */
    public function testDefaultProperties()
    {
        $collectionCreateStruct = new CollectionCreateStruct();

        self::assertNull($collectionCreateStruct->name);
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\CollectionCreateStruct::__construct
     */
    public function testSetProperties()
    {
        $collectionCreateStruct = new CollectionCreateStruct(
            array(
                'name' => 'My collection',
            )
        );

        self::assertEquals('My collection', $collectionCreateStruct->name);
    }
}
