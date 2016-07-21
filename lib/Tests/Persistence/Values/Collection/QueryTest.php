<?php

namespace Netgen\BlockManager\Tests\Persistence\Values\Collection;

use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Collection\Query;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    public function testSetDefaultProperties()
    {
        $query = new Query();

        $this->assertNull($query->id);
        $this->assertNull($query->collectionId);
        $this->assertNull($query->position);
        $this->assertNull($query->identifier);
        $this->assertNull($query->type);
        $this->assertNull($query->parameters);
        $this->assertNull($query->status);
    }

    public function testSetProperties()
    {
        $query = new Query(
            array(
                'id' => 42,
                'collectionId' => 30,
                'position' => 3,
                'identifier' => 'my_query',
                'type' => 'ezcontent_search',
                'parameters' => array('param' => 'value'),
                'status' => Collection::STATUS_PUBLISHED,
            )
        );

        $this->assertEquals(42, $query->id);
        $this->assertEquals(30, $query->collectionId);
        $this->assertEquals(3, $query->position);
        $this->assertEquals('my_query', $query->identifier);
        $this->assertEquals('ezcontent_search', $query->type);
        $this->assertEquals(array('param' => 'value'), $query->parameters);
        $this->assertEquals(Collection::STATUS_PUBLISHED, $query->status);
    }
}
