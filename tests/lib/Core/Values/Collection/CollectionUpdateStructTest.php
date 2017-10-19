<?php

namespace Netgen\BlockManager\Tests\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\CollectionUpdateStruct;
use PHPUnit\Framework\TestCase;

class CollectionUpdateStructTest extends TestCase
{
    public function testDefaultProperties()
    {
        $collectionUpdateStruct = new CollectionUpdateStruct();

        $this->assertNull($collectionUpdateStruct->offset);
        $this->assertNull($collectionUpdateStruct->limit);
    }

    public function testSetProperties()
    {
        $collectionUpdateStruct = new CollectionUpdateStruct(
            array(
                'offset' => 6,
                'limit' => 3,
            )
        );

        $this->assertEquals(6, $collectionUpdateStruct->offset);
        $this->assertEquals(3, $collectionUpdateStruct->limit);
    }
}
