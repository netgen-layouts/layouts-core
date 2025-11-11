<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters;

use Netgen\Layouts\Parameters\ParameterType;
use Netgen\Layouts\Parameters\Registry\ParameterTypeRegistry;
use Netgen\Layouts\Parameters\TranslatableParameterBuilder;
use Netgen\Layouts\Parameters\TranslatableParameterBuilderFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TranslatableParameterBuilderFactory::class)]
final class TranslatableParameterBuilderFactoryTest extends TestCase
{
    private ParameterTypeRegistry $registry;

    private TranslatableParameterBuilderFactory $factory;

    protected function setUp(): void
    {
        $this->registry = new ParameterTypeRegistry(
            [
                new ParameterType\TextType(),
                new ParameterType\Compound\BooleanType(),
            ],
        );

        $this->factory = new TranslatableParameterBuilderFactory($this->registry);
    }

    public function testCreateParameterBuilder(): void
    {
        $parameterBuilder = $this->factory->createParameterBuilder();

        self::assertInstanceOf(TranslatableParameterBuilder::class, $parameterBuilder);
        self::assertNull($parameterBuilder->getName());
        self::assertTrue($parameterBuilder->getOption('translatable'));
        self::assertNull($parameterBuilder->getType());
    }

    public function testCreateParameterBuilderWithNoOptions(): void
    {
        $parameterBuilder = $this->factory->createParameterBuilder(
            [
                'name' => 'param',
                'type' => ParameterType\TextType::class,
            ],
        );

        self::assertInstanceOf(TranslatableParameterBuilder::class, $parameterBuilder);
        self::assertSame('param', $parameterBuilder->getName());
        self::assertTrue($parameterBuilder->getOption('translatable'));

        self::assertSame(
            $this->registry->getParameterTypeByClass(ParameterType\TextType::class),
            $parameterBuilder->getType(),
        );
    }

    public function testCreateParameterBuilderWithConfig(): void
    {
        $parameterBuilder = $this->factory->createParameterBuilder(
            [
                'name' => 'param',
                'type' => ParameterType\TextType::class,
                'options' => [
                    'translatable' => false,
                ],
            ],
        );

        self::assertInstanceOf(TranslatableParameterBuilder::class, $parameterBuilder);
        self::assertSame('param', $parameterBuilder->getName());
        self::assertSame(['translatable' => false], $parameterBuilder->getOptions());

        self::assertSame(
            $this->registry->getParameterTypeByClass(ParameterType\TextType::class),
            $parameterBuilder->getType(),
        );
    }
}
