<?php

namespace Netgen\BlockManager\Tests\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Query;

class QueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::__construct
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getCollectionId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getPosition
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getType
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getParameters
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getParameter
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::hasParameter
     */
    public function testSetDefaultProperties()
    {
        $query = new Query();

        self::assertNull($query->getId());
        self::assertNull($query->getStatus());
        self::assertNull($query->getCollectionId());
        self::assertNull($query->getPosition());
        self::assertNull($query->getIdentifier());
        self::assertNull($query->getType());
        self::assertEquals(array(), $query->getParameters());
        self::assertNull($query->getParameter('test'));
        self::assertFalse($query->hasParameter('test'));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::__construct
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getCollectionId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getPosition
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getType
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getParameters
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getParameter
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::hasParameter
     */
    public function testSetProperties()
    {
        $query = new Query(
            array(
                'id' => 42,
                'status' => Collection::STATUS_PUBLISHED,
                'collectionId' => 30,
                'position' => 3,
                'identifier' => 'my_query',
                'type' => 'ezcontent_search',
                'parameters' => array('param' => 'value'),
            )
        );

        self::assertEquals(42, $query->getId());
        self::assertEquals(Collection::STATUS_PUBLISHED, $query->getStatus());
        self::assertEquals(30, $query->getCollectionId());
        self::assertEquals(3, $query->getPosition());
        self::assertEquals('my_query', $query->getIdentifier());
        self::assertEquals('ezcontent_search', $query->getType());
        self::assertEquals(array('param' => 'value'), $query->getParameters());
        self::assertNull($query->getParameter('test'));
        self::assertEquals('value', $query->getParameter('param'));
        self::assertFalse($query->hasParameter('test'));
        self::assertTrue($query->hasParameter('param'));
    }
}
