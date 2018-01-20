<?php

namespace Netgen\BlockManager\Tests\Collection;

use Netgen\BlockManager\Collection\QueryType\Configuration\Configuration;
use Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface;
use Netgen\BlockManager\Collection\QueryTypeFactory;
use Netgen\BlockManager\Collection\QueryTypeInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderFactoryInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use PHPUnit\Framework\TestCase;

final class QueryTypeFactoryTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $parameterBuilderFactoryMock;

    /**
     * @var \Netgen\BlockManager\Collection\QueryTypeFactory
     */
    private $factory;

    public function setUp()
    {
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
            new Configuration()
        );

        $this->assertInstanceOf(QueryTypeInterface::class, $queryType);
        $this->assertEquals('type', $queryType->getType());
        $this->assertEquals(new Configuration(), $queryType->getConfig());
    }
}
