<?php

namespace Netgen\BlockManager\Tests\Collection;

use Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface;
use Netgen\BlockManager\Collection\QueryType\Configuration\Configuration;
use Netgen\BlockManager\Collection\QueryType;
use Netgen\BlockManager\Parameters\Parameter;

class QueryTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $handlerMock;

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
        $this->handlerMock = $this->getMock(QueryTypeHandlerInterface::class);

        $this->configMock = $this->getMockBuilder(Configuration::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->queryType = new QueryType(
            'query_type',
            $this->handlerMock,
            $this->configMock
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType::__construct
     * @covers \Netgen\BlockManager\Collection\QueryType::getType
     */
    public function testGetType()
    {
        self::assertEquals('query_type', $this->queryType->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType::getParameters
     */
    public function testGetParameters()
    {
        $this->handlerMock
            ->expects($this->once())
            ->method('getParameters')
            ->will($this->returnValue(array('params')));

        self::assertEquals(array('params'), $this->queryType->getParameters());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType::getValues
     */
    public function testGetValues()
    {
        $this->handlerMock
            ->expects($this->once())
            ->method('getValues')
            ->with($this->equalTo(array('params')), $this->equalTo(5), $this->equalTo(10))
            ->will($this->returnValue(array('values')));

        self::assertEquals(
            array('values'),
            $this->queryType->getValues(array('params'), 5, 10)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType::getCount
     */
    public function testGetCount()
    {
        $this->handlerMock
            ->expects($this->once())
            ->method('getCount')
            ->with($this->equalTo(array('params')))
            ->will($this->returnValue(6));

        self::assertEquals(6, $this->queryType->getCount(array('params')));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType::getConfig
     */
    public function testGetConfig()
    {
        self::assertEquals($this->configMock, $this->queryType->getConfig());
    }
}
