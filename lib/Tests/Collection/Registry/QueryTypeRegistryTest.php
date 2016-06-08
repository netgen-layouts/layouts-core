<?php

namespace Netgen\BlockManager\Tests\Collection\Registry;

use Netgen\BlockManager\Collection\QueryType\Configuration\Configuration;
use Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistry;

class QueryTypeRegistryTest extends \PHPUnit_Framework_TestCase
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

        $configMock = $this->createMock(Configuration::class);

        $this->queryType = new QueryType(
            'query_type',
            $this->createMock(QueryTypeHandlerInterface::class),
            $configMock
        );

        $this->registry->addQueryType($this->queryType);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\QueryTypeRegistry::addQueryType
     * @covers \Netgen\BlockManager\Collection\Registry\QueryTypeRegistry::getQueryTypes
     */
    public function testAddQueryType()
    {
        self::assertEquals(array('query_type' => $this->queryType), $this->registry->getQueryTypes());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\QueryTypeRegistry::getQueryType
     */
    public function testGetQueryType()
    {
        self::assertEquals($this->queryType, $this->registry->getQueryType('query_type'));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\QueryTypeRegistry::getQueryType
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testGetQueryTypeThrowsNotFoundException()
    {
        $this->registry->getQueryType('other_query_type');
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\QueryTypeRegistry::hasQueryType
     */
    public function testHasQueryType()
    {
        self::assertTrue($this->registry->hasQueryType('query_type'));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Registry\QueryTypeRegistry::hasQueryType
     */
    public function testHasQueryTypeWithNoQueryType()
    {
        self::assertFalse($this->registry->hasQueryType('other_query_type'));
    }
}
