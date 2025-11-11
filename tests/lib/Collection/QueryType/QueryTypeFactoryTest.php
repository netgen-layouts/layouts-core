<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\QueryType;

use Netgen\Layouts\Collection\QueryType\QueryTypeFactory;
use Netgen\Layouts\Collection\QueryType\QueryTypeHandlerInterface;
use Netgen\Layouts\Parameters\ParameterBuilderFactoryInterface;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(QueryTypeFactory::class)]
final class QueryTypeFactoryTest extends TestCase
{
    private MockObject $parameterBuilderFactoryMock;

    private QueryTypeFactory $factory;

    protected function setUp(): void
    {
        $this->parameterBuilderFactoryMock = $this->createMock(ParameterBuilderFactoryInterface::class);
        $this->parameterBuilderFactoryMock
            ->method('createParameterBuilder')
            ->willReturn($this->createMock(ParameterBuilderInterface::class));

        $this->factory = new QueryTypeFactory($this->parameterBuilderFactoryMock);
    }

    public function testBuildQueryType(): void
    {
        $queryType = $this->factory->buildQueryType(
            'type',
            $this->createMock(QueryTypeHandlerInterface::class),
            [
                'enabled' => false,
                'name' => 'Query type',
            ],
        );

        self::assertSame('type', $queryType->getType());

        self::assertFalse($queryType->isEnabled());
        self::assertSame('Query type', $queryType->getName());
    }

    public function testBuildQueryTypeWithEmptyName(): void
    {
        $queryType = $this->factory->buildQueryType(
            'type',
            $this->createMock(QueryTypeHandlerInterface::class),
            [
                'enabled' => true,
            ],
        );

        self::assertSame('type', $queryType->getType());

        self::assertTrue($queryType->isEnabled());
        self::assertSame('', $queryType->getName());
    }
}
