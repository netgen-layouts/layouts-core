<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\QueryType;

use Netgen\BlockManager\Collection\QueryType\QueryTypeFactory;
use Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface;
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

    public function setUp(): void
    {
        $this->parameterBuilderFactoryMock = $this->createMock(ParameterBuilderFactoryInterface::class);
        $this->parameterBuilderFactoryMock
            ->expects(self::any())
            ->method('createParameterBuilder')
            ->willReturn($this->createMock(ParameterBuilderInterface::class));

        $this->factory = new QueryTypeFactory($this->parameterBuilderFactoryMock);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType\QueryTypeFactory::__construct
     * @covers \Netgen\BlockManager\Collection\QueryType\QueryTypeFactory::buildQueryType
     */
    public function testBuildQueryType(): void
    {
        $queryType = $this->factory->buildQueryType(
            'type',
            $this->createMock(QueryTypeHandlerInterface::class),
            [
                'enabled' => false,
                'name' => 'Query type',
            ]
        );

        self::assertSame('type', $queryType->getType());

        self::assertFalse($queryType->isEnabled());
        self::assertSame('Query type', $queryType->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType\QueryTypeFactory::__construct
     * @covers \Netgen\BlockManager\Collection\QueryType\QueryTypeFactory::buildQueryType
     */
    public function testBuildQueryTypeWithEmptyName(): void
    {
        $queryType = $this->factory->buildQueryType(
            'type',
            $this->createMock(QueryTypeHandlerInterface::class),
            [
                'enabled' => true,
            ]
        );

        self::assertSame('type', $queryType->getType());

        self::assertTrue($queryType->isEnabled());
        self::assertSame('', $queryType->getName());
    }
}
