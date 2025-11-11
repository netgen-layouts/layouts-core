<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters;

use Netgen\Layouts\Parameters\ParameterBuilder;
use Netgen\Layouts\Parameters\ParameterBuilderFactory;
use Netgen\Layouts\Parameters\ParameterType;
use Netgen\Layouts\Parameters\Registry\ParameterTypeRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ParameterBuilderFactory::class)]
final class ParameterBuilderFactoryTest extends TestCase
{
    private ParameterTypeRegistry $registry;

    private ParameterBuilderFactory $factory;

    protected function setUp(): void
    {
        $this->registry = new ParameterTypeRegistry(
            [
                new ParameterType\TextType(),
                new ParameterType\Compound\BooleanType(),
            ],
        );

        $this->factory = new ParameterBuilderFactory($this->registry);
    }

    public function testCreateParameterBuilder(): void
    {
        $parameterBuilder = $this->factory->createParameterBuilder();

        self::assertInstanceOf(ParameterBuilder::class, $parameterBuilder);
        self::assertNull($parameterBuilder->getName());
        self::assertNull($parameterBuilder->getType());
    }

    public function testCreateParameterBuilderWithConfig(): void
    {
        $parameterBuilder = $this->factory->createParameterBuilder(
            [
                'name' => 'param',
                'type' => ParameterType\TextType::class,
            ],
        );

        self::assertInstanceOf(ParameterBuilder::class, $parameterBuilder);
        self::assertSame('param', $parameterBuilder->getName());

        self::assertSame(
            $this->registry->getParameterTypeByClass(ParameterType\TextType::class),
            $parameterBuilder->getType(),
        );
    }
}
