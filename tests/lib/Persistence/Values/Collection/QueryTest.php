<?php

namespace Netgen\BlockManager\Tests\Persistence\Values\Collection;

use Netgen\BlockManager\Persistence\Values\Collection\Query;
use Netgen\BlockManager\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

final class QueryTest extends TestCase
{
    public function testSetDefaultProperties()
    {
        $query = new Query();

        $this->assertNull($query->id);
        $this->assertNull($query->collectionId);
        $this->assertNull($query->type);
        $this->assertNull($query->parameters);
        $this->assertNull($query->status);
    }

    public function testSetProperties()
    {
        $query = new Query(
            [
                'id' => 42,
                'collectionId' => 30,
                'type' => 'ezcontent_search',
                'parameters' => ['param' => 'value'],
                'status' => Value::STATUS_PUBLISHED,
            ]
        );

        $this->assertEquals(42, $query->id);
        $this->assertEquals(30, $query->collectionId);
        $this->assertEquals('ezcontent_search', $query->type);
        $this->assertEquals(['param' => 'value'], $query->parameters);
        $this->assertEquals(Value::STATUS_PUBLISHED, $query->status);
    }
}
