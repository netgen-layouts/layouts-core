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
        $this->assertEquals(array(), $collectionCreateStruct->itemCreateStructs);
        $this->assertNull($collectionCreateStruct->queryCreateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct::__construct
     */
    public function testSetProperties()
    {
        $collectionCreateStruct = new CollectionCreateStruct(
            array(
                'type' => Collection::TYPE_DYNAMIC,
            )
        );

        $this->assertEquals(Collection::TYPE_DYNAMIC, $collectionCreateStruct->type);
    }
}
