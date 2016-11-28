<?php

namespace Netgen\BlockManager\Tests\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct;
use PHPUnit\Framework\TestCase;

class CollectionCreateStructTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct::__construct
     */
    public function testDefaultProperties()
    {
        $collectionCreateStruct = new CollectionCreateStruct();

        $this->assertEquals(Collection::TYPE_MANUAL, $collectionCreateStruct->type);
        $this->assertNull($collectionCreateStruct->name);
        $this->assertNull($collectionCreateStruct->shared);
        $this->assertEquals(array(), $collectionCreateStruct->itemCreateStructs);
        $this->assertEquals(array(), $collectionCreateStruct->queryCreateStructs);
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct::__construct
     */
    public function testSetProperties()
    {
        $collectionCreateStruct = new CollectionCreateStruct(
            array(
                'type' => Collection::TYPE_DYNAMIC,
                'name' => 'My collection',
                'shared' => true,
            )
        );

        $this->assertEquals(Collection::TYPE_DYNAMIC, $collectionCreateStruct->type);
        $this->assertTrue($collectionCreateStruct->shared);
        $this->assertEquals('My collection', $collectionCreateStruct->name);
    }
}
