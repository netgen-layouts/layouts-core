<?php

namespace Netgen\BlockManager\Tests\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::__construct
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getCollectionId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getQueryType
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getParameters
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getParameter
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::hasParameter
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::isPublished
     */
    public function testSetDefaultProperties()
    {
        $query = new Query();

        $this->assertNull($query->getId());
        $this->assertNull($query->getStatus());
        $this->assertNull($query->getCollectionId());
        $this->assertNull($query->getQueryType());
        $this->assertNull($query->isPublished());
        $this->assertEquals(array(), $query->getParameters());
        $this->assertFalse($query->hasParameter('test'));

        try {
            $query->getParameter('test');
        } catch (InvalidArgumentException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::__construct
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getCollectionId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getQueryType
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getParameters
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getParameter
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::hasParameter
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::isPublished
     */
    public function testSetProperties()
    {
        $query = new Query(
            array(
                'id' => 42,
                'status' => Value::STATUS_PUBLISHED,
                'collectionId' => 30,
                'queryType' => new QueryType('query_type'),
                'parameters' => array('param' => 'value'),
                'published' => true,
            )
        );

        $this->assertEquals(42, $query->getId());
        $this->assertTrue($query->isPublished());
        $this->assertEquals(30, $query->getCollectionId());
        $this->assertEquals(new QueryType('query_type'), $query->getQueryType());
        $this->assertEquals(array('param' => 'value'), $query->getParameters());
        $this->assertEquals('value', $query->getParameter('param'));
        $this->assertFalse($query->hasParameter('test'));
        $this->assertTrue($query->hasParameter('param'));
        $this->assertTrue($query->isPublished());

        try {
            $query->getParameter('test');
        } catch (InvalidArgumentException $e) {
            // Do nothing
        }
    }
}
