<?php

namespace Netgen\BlockManager\Tests\Collection\Registry;

use Netgen\BlockManager\Collection\Registry\QueryTypeRegistry;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\TestCase;

class QueryTypeRegistryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Collection\QueryTypeInterface
     */
    protected $queryType;

    /**
     * @var \Netgen\BlockManager\Collection\Registry\QueryTypeRegistry
     */
    protected $registry;

    public function setUp()
    {
        $this->registry = new QueryTypeRegistry();

        $this->queryType = new QueryType('query_type');

        $this->registry->addQueryType('query_type', $this->queryType);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\QueryTypeRegistry::addQueryType
     * @covers \Netgen\BlockManager\Collection\Registry\QueryTypeRegistry::getQueryTypes
     */
    public function testAddQueryType()
    {
        $this->assertEquals(array('query_type' => $this->queryType), $this->registry->getQueryTypes());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\QueryTypeRegistry::getQueryType
     */
    public function testGetQueryType()
    {
        $this->assertEquals($this->queryType, $this->registry->getQueryType('query_type'));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\QueryTypeRegistry::getQueryType
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     * @expectedExceptionMessage Query type "other_query_type" does not exist.
     */
    public function testGetQueryTypeThrowsInvalidArgumentException()
    {
        $this->registry->getQueryType('other_query_type');
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\QueryTypeRegistry::hasQueryType
     */
    public function testHasQueryType()
    {
        $this->assertTrue($this->registry->hasQueryType('query_type'));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\QueryTypeRegistry::hasQueryType
     */
    public function testHasQueryTypeWithNoQueryType()
    {
        $this->assertFalse($this->registry->hasQueryType('other_query_type'));
    }
}
