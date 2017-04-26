<?php

namespace Netgen\BlockManager\Tests\Collection;

use Netgen\BlockManager\Collection\QueryType\Configuration\Configuration;
use Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface;
use Netgen\BlockManager\Collection\QueryTypeFactory;
use Netgen\BlockManager\Collection\QueryTypeInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderFactoryInterface;
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
    protected $parameterBuilderFactoryMock;

    /**
     * @var \Netgen\BlockManager\Collection\QueryTypeFactory
     */
    protected $factory;

    public function setUp()
    {
        $this->configMock = $this->createMock(Configuration::class);

        $this->parameterBuilderFactoryMock = $this->createMock(ParameterBuilderFactoryInterface::class);
        $this->parameterBuilderFactoryMock
            ->expects($this->any())
            ->method('createParameterBuilder')
            ->will(
                $this->returnValue(
                    $this->createMock(ParameterBuilderInterface::class)
                )
            );

        $this->factory = new QueryTypeFactory($this->parameterBuilderFactoryMock);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryTypeFactory::__construct
     * @covers \Netgen\BlockManager\Collection\QueryTypeFactory::buildQueryType
     */
    public function testBuildQueryType()
    {
        $queryType = $this->factory->buildQueryType(
            'type',
            $this->createMock(QueryTypeHandlerInterface::class),
            $this->configMock
        );

        $this->assertInstanceOf(QueryTypeInterface::class, $queryType);
        $this->assertEquals('type', $queryType->getType());
        $this->assertEquals($this->configMock, $queryType->getConfig());
    }
}
