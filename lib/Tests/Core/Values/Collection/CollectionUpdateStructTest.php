<?php

namespace Netgen\BlockManager\Tests\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\CollectionUpdateStruct;
use PHPUnit\Framework\TestCase;

class CollectionUpdateStructTest extends TestCase
{
    public function testDefaultProperties()
    {
        $collectionUpdateStruct = new CollectionUpdateStruct();

        $this->assertNull($collectionUpdateStruct->name);
    }

    public function testSetProperties()
    {
        $collectionUpdateStruct = new CollectionUpdateStruct(
            array(
                'name' => 'My collection',
            )
        );

        $this->assertEquals('My collection', $collectionUpdateStruct->name);
    }
}
