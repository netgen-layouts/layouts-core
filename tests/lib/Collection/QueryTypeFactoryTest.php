<?php

namespace Netgen\BlockManager\Tests\Collection;

use Netgen\BlockManager\Collection\QueryType\QueryTypeFactory;
use Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface;
use Netgen\BlockManager\Collection\QueryType\QueryTypeInterface;
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
     * @var \Netgen\BlockManager\Collection\QueryType\QueryTypeFactory
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
     * @covers \Netgen\BlockManager\Collection\QueryType\QueryTypeFactory::__construct
     * @covers \Netgen\BlockManager\Collection\QueryType\QueryTypeFactory::buildQueryType
     */
    public function testBuildQueryType()
    {
        $queryType = $this->factory->buildQueryType(
            'type',
            $this->createMock(QueryTypeHandlerInterface::class),
            [
                'enabled' => false,
                'name' => 'Query type',
            ]
        );

        $this->assertInstanceOf(QueryTypeInterface::class, $queryType);
        $this->assertEquals('type', $queryType->getType());

        $this->assertFalse($queryType->isEnabled());
        $this->assertEquals('Query type', $queryType->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType\QueryTypeFactory::__construct
     * @covers \Netgen\BlockManager\Collection\QueryType\QueryTypeFactory::buildQueryType
     */
    public function testBuildQueryTypeWithEmptyName()
    {
        $queryType = $this->factory->buildQueryType(
            'type',
            $this->createMock(QueryTypeHandlerInterface::class),
            [
                'enabled' => true,
            ]
        );

        $this->assertInstanceOf(QueryTypeInterface::class, $queryType);
        $this->assertEquals('type', $queryType->getType());

        $this->assertTrue($queryType->isEnabled());
        $this->assertEquals('', $queryType->getName());
    }
}
