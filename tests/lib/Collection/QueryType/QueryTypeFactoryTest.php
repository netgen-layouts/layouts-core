<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\QueryType;

use Netgen\Layouts\Collection\QueryType\QueryTypeFactory;
use Netgen\Layouts\Collection\QueryType\QueryTypeHandlerInterface;
use Netgen\Layouts\Parameters\ParameterBuilderFactory;
use Netgen\Layouts\Parameters\Registry\ParameterTypeRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(QueryTypeFactory::class)]
final class QueryTypeFactoryTest extends TestCase
{
    private QueryTypeFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new QueryTypeFactory(new ParameterBuilderFactory(new ParameterTypeRegistry([])));
    }

    public function testBuildQueryType(): void
    {
        $queryType = $this->factory->buildQueryType(
            'type',
            self::createStub(QueryTypeHandlerInterface::class),
            [
                'enabled' => false,
                'name' => 'Query type',
            ],
        );

        self::assertSame('type', $queryType->type);

        self::assertFalse($queryType->isEnabled);
        self::assertSame('Query type', $queryType->name);
    }

    public function testBuildQueryTypeWithEmptyName(): void
    {
        $queryType = $this->factory->buildQueryType(
            'type',
            self::createStub(QueryTypeHandlerInterface::class),
            [
                'enabled' => true,
            ],
        );

        self::assertSame('type', $queryType->type);

        self::assertTrue($queryType->isEnabled);
        self::assertSame('', $queryType->name);
    }
}
