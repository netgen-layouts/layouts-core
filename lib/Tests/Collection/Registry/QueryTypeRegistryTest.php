<?php

namespace Netgen\BlockManager\Tests\Collection\Registry;

use Netgen\BlockManager\Collection\QueryType\Configuration\Configuration;
use Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface;
use Netgen\BlockManager\Collection\QueryType;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistry;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryTypeHandler;
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

        $configMock = $this->createMock(Configuration::class);

        $this->queryType = new QueryType(
            'query_type',
            new QueryTypeHandler(),
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
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
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
