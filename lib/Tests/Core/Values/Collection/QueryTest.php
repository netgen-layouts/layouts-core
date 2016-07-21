<?php

namespace Netgen\BlockManager\Tests\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Query;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
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

        $this->assertNull($query->getId());
        $this->assertNull($query->getStatus());
        $this->assertNull($query->getCollectionId());
        $this->assertNull($query->getPosition());
        $this->assertNull($query->getIdentifier());
        $this->assertNull($query->getType());
        $this->assertEquals(array(), $query->getParameters());
        $this->assertNull($query->getParameter('test'));
        $this->assertFalse($query->hasParameter('test'));
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

        $this->assertEquals(42, $query->getId());
        $this->assertEquals(Collection::STATUS_PUBLISHED, $query->getStatus());
        $this->assertEquals(30, $query->getCollectionId());
        $this->assertEquals(3, $query->getPosition());
        $this->assertEquals('my_query', $query->getIdentifier());
        $this->assertEquals('ezcontent_search', $query->getType());
        $this->assertEquals(array('param' => 'value'), $query->getParameters());
        $this->assertNull($query->getParameter('test'));
        $this->assertEquals('value', $query->getParameter('param'));
        $this->assertFalse($query->hasParameter('test'));
        $this->assertTrue($query->hasParameter('param'));
    }
}
