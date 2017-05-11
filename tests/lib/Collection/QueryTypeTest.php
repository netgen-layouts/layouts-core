<?php

namespace Netgen\BlockManager\Tests\Collection;

use Netgen\BlockManager\Collection\QueryType;
use Netgen\BlockManager\Collection\QueryType\Configuration\Configuration;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryTypeHandler;
use PHPUnit\Framework\TestCase;

class QueryTypeTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Netgen\BlockManager\Collection\QueryType
     */
    protected $queryType;

    public function setUp()
    {
        $this->configMock = $this->createMock(Configuration::class);

        $this->queryType = new QueryType(
            array(
                'type' => 'query_type',
                'handler' => new QueryTypeHandler(array('val1', 'val2'), null, 3),
                'config' => $this->configMock,
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType::__construct
     * @covers \Netgen\BlockManager\Collection\QueryType::getType
     */
    public function testGetType()
    {
        $this->assertEquals('query_type', $this->queryType->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType::getValues
     */
    public function testGetValues()
    {
        $this->assertEquals(array('val1', 'val2'), $this->queryType->getValues(new Query()));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType::getConfig
     */
    public function testGetConfig()
    {
        $this->assertEquals($this->configMock, $this->queryType->getConfig());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType::getCount
     */
    public function testGetCount()
    {
        $this->assertEquals(2, $this->queryType->getCount(new Query()));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType::getCount
     */
    public function testGetCountWithInternalLimit()
    {
        $queryType = new QueryType(
            array(
                'type' => 'query_type',
                'handler' => new QueryTypeHandler(array('val1', 'val2'), null, 1),
                'config' => $this->configMock,
            )
        );

        $this->assertEquals(1, $queryType->getCount(new Query()));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType::getInternalLimit
     */
    public function testGetInternalLimit()
    {
        $this->assertEquals(3, $this->queryType->getInternalLimit(new Query()));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType::isConfigured
     */
    public function testIsConfigured()
    {
        $this->assertTrue($this->queryType->isConfigured(new Query()));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType::isContextual
     */
    public function testIsContextual()
    {
        $this->assertFalse($this->queryType->isContextual(new Query()));
    }
}
