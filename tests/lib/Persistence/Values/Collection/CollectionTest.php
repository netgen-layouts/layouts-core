<?php

namespace Netgen\BlockManager\Tests\Persistence\Values\Collection;

use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

final class CollectionTest extends TestCase
{
    public function testSetProperties()
    {
        $collection = new Collection(
            [
                'id' => 42,
                'status' => Value::STATUS_PUBLISHED,
            ]
        );

        $this->assertEquals(42, $collection->id);
        $this->assertEquals(Value::STATUS_PUBLISHED, $collection->status);
    }
}
