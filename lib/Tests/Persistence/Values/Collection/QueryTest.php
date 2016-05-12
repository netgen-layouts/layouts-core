<?php

namespace Netgen\BlockManager\Tests\Persistence\Values\Collection;

use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Collection\Query;

class QueryTest extends \PHPUnit_Framework_TestCase
{
    public function testSetDefaultProperties()
    {
        $query = new Query();

        self::assertNull($query->id);
        self::assertNull($query->collectionId);
        self::assertNull($query->position);
        self::assertNull($query->identifier);
        self::assertNull($query->type);
        self::assertNull($query->parameters);
        self::assertNull($query->status);
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

        self::assertEquals(42, $query->id);
        self::assertEquals(30, $query->collectionId);
        self::assertEquals(3, $query->position);
        self::assertEquals('my_query', $query->identifier);
        self::assertEquals('ezcontent_search', $query->type);
        self::assertEquals(array('param' => 'value'), $query->parameters);
        self::assertEquals(Collection::STATUS_PUBLISHED, $query->status);
    }
}
