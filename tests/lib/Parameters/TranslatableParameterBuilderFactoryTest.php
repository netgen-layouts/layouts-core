<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry;
use Netgen\BlockManager\Parameters\TranslatableParameterBuilder;
use Netgen\BlockManager\Parameters\TranslatableParameterBuilderFactory;
use PHPUnit\Framework\TestCase;

final class TranslatableParameterBuilderFactoryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistryInterface
     */
    private $registry;

    /**
     * @var \Netgen\BlockManager\Parameters\TranslatableParameterBuilderFactory
     */
    private $factory;

    public function setUp(): void
    {
        $this->registry = new ParameterTypeRegistry();
        $this->registry->addParameterType(new ParameterType\TextType());
        $this->registry->addParameterType(new ParameterType\Compound\BooleanType());

        $this->factory = new TranslatableParameterBuilderFactory($this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\TranslatableParameterBuilderFactory::createParameterBuilder
     * @covers \Netgen\BlockManager\Parameters\TranslatableParameterBuilderFactory::resolveOptions
     */
    public function testCreateParameterBuilder(): void
    {
        $parameterBuilder = $this->factory->createParameterBuilder();

        $this->assertInstanceOf(TranslatableParameterBuilder::class, $parameterBuilder);
        $this->assertNull($parameterBuilder->getName());
        $this->assertTrue($parameterBuilder->getOption('translatable'));
        $this->assertNull($parameterBuilder->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\TranslatableParameterBuilderFactory::createParameterBuilder
     * @covers \Netgen\BlockManager\Parameters\TranslatableParameterBuilderFactory::resolveOptions
     */
    public function testCreateParameterBuilderWithNoOptions(): void
    {
        $parameterBuilder = $this->factory->createParameterBuilder(
            [
                'name' => 'param',
                'type' => ParameterType\TextType::class,
            ]
        );

        $this->assertInstanceOf(TranslatableParameterBuilder::class, $parameterBuilder);
        $this->assertSame('param', $parameterBuilder->getName());
        $this->assertTrue($parameterBuilder->getOption('translatable'));

        $this->assertSame(
            $this->registry->getParameterTypeByClass(ParameterType\TextType::class),
            $parameterBuilder->getType()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\TranslatableParameterBuilderFactory::createParameterBuilder
     * @covers \Netgen\BlockManager\Parameters\TranslatableParameterBuilderFactory::resolveOptions
     */
    public function testCreateParameterBuilderWithConfig(): void
    {
        $parameterBuilder = $this->factory->createParameterBuilder(
            [
                'name' => 'param',
                'type' => ParameterType\TextType::class,
                'options' => [
                    'translatable' => false,
                ],
            ]
        );

        $this->assertInstanceOf(TranslatableParameterBuilder::class, $parameterBuilder);
        $this->assertSame('param', $parameterBuilder->getName());
        $this->assertSame(['translatable' => false], $parameterBuilder->getOptions());

        $this->assertSame(
            $this->registry->getParameterTypeByClass(ParameterType\TextType::class),
            $parameterBuilder->getType()
        );
    }
}
