<?php

namespace Netgen\BlockManager\Tests\Collection;

use Netgen\BlockManager\Collection\QueryType\Configuration\Configuration;
use Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface;
use Netgen\BlockManager\Collection\QueryTypeFactory;
use Netgen\BlockManager\Collection\QueryTypeInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use PHPUnit\Framework\TestCase;

class QueryTypeFactoryTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $parameterBuilderMock;

    public function setUp()
    {
        $this->configMock = $this->createMock(Configuration::class);
        $this->parameterBuilderMock = $this->createMock(ParameterBuilderInterface::class);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryTypeFactory::buildQueryType
     */
    public function testBuildQueryType()
    {
        $queryType = QueryTypeFactory::buildQueryType(
            'type',
            $this->createMock(QueryTypeHandlerInterface::class),
            $this->configMock,
            $this->parameterBuilderMock
        );

        $this->assertInstanceOf(QueryTypeInterface::class, $queryType);
        $this->assertEquals('type', $queryType->getType());
        $this->assertEquals($this->configMock, $queryType->getConfig());
    }
}
