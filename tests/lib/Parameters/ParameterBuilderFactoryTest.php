<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Parameters\ParameterBuilder;
use Netgen\BlockManager\Parameters\ParameterBuilderFactory;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry;
use PHPUnit\Framework\TestCase;

final class ParameterBuilderFactoryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistryInterface
     */
    private $registry;

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterBuilderFactory
     */
    private $factory;

    public function setUp(): void
    {
        $this->registry = new ParameterTypeRegistry(
            [
                new ParameterType\TextType(),
                new ParameterType\Compound\BooleanType(),
            ]
        );

        $this->factory = new ParameterBuilderFactory($this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilderFactory::__construct
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilderFactory::createParameterBuilder
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilderFactory::resolveOptions
     */
    public function testCreateParameterBuilder(): void
    {
        $parameterBuilder = $this->factory->createParameterBuilder();

        self::assertInstanceOf(ParameterBuilder::class, $parameterBuilder);
        self::assertNull($parameterBuilder->getName());
        self::assertNull($parameterBuilder->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilderFactory::createParameterBuilder
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilderFactory::resolveOptions
     */
    public function testCreateParameterBuilderWithConfig(): void
    {
        $parameterBuilder = $this->factory->createParameterBuilder(
            [
                'name' => 'param',
                'type' => ParameterType\TextType::class,
            ]
        );

        self::assertInstanceOf(ParameterBuilder::class, $parameterBuilder);
        self::assertSame('param', $parameterBuilder->getName());

        self::assertSame(
            $this->registry->getParameterTypeByClass(ParameterType\TextType::class),
            $parameterBuilder->getType()
        );
    }
}
