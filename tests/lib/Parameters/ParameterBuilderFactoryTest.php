<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters;

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

        self::assertNull($parameterBuilder->getName());
        self::assertFalse($parameterBuilder->hasOption('translatable'));
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

        self::assertSame('param', $parameterBuilder->getName());
        self::assertFalse($parameterBuilder->hasOption('translatable'));

        self::assertSame(
            $this->registry->getParameterTypeByClass(ParameterType\TextType::class),
            $parameterBuilder->getType(),
        );
    }

    public function testCreateTranslatableParameterBuilder(): void
    {
        $parameterBuilder = $this->factory->createParameterBuilder([], true);

        self::assertNull($parameterBuilder->getName());
        self::assertTrue($parameterBuilder->hasOption('translatable'));
        self::assertTrue($parameterBuilder->getOption('translatable'));
        self::assertNull($parameterBuilder->getType());
    }

    public function testCreateTranslatableParameterBuilderWithNoOptions(): void
    {
        $parameterBuilder = $this->factory->createParameterBuilder(
            [
                'name' => 'param',
                'type' => ParameterType\TextType::class,
            ],
            true,
        );

        self::assertSame('param', $parameterBuilder->getName());
        self::assertTrue($parameterBuilder->hasOption('translatable'));
        self::assertTrue($parameterBuilder->getOption('translatable'));

        self::assertSame(
            $this->registry->getParameterTypeByClass(ParameterType\TextType::class),
            $parameterBuilder->getType(),
        );
    }

    public function testCreateTranslatableParameterBuilderWithConfig(): void
    {
        $parameterBuilder = $this->factory->createParameterBuilder(
            [
                'name' => 'param',
                'type' => ParameterType\TextType::class,
                'options' => [
                    'translatable' => false,
                ],
            ],
            true,
        );

        self::assertSame('param', $parameterBuilder->getName());
        self::assertTrue($parameterBuilder->hasOption('translatable'));
        self::assertSame(['translatable' => false], $parameterBuilder->getOptions());

        self::assertSame(
            $this->registry->getParameterTypeByClass(ParameterType\TextType::class),
            $parameterBuilder->getType(),
        );
    }
}
