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

        $this->assertEquals(new TranslatableParameterBuilder($this->factory), $parameterBuilder);
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

        $this->assertEquals(
            new TranslatableParameterBuilder(
                $this->factory,
                'param',
                $this->registry->getParameterTypeByClass(ParameterType\TextType::class),
                [
                    'translatable' => true,
                ]
            ),
            $parameterBuilder
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

        $this->assertEquals(
            new TranslatableParameterBuilder(
                $this->factory,
                'param',
                $this->registry->getParameterTypeByClass(ParameterType\TextType::class),
                [
                    'translatable' => false,
                ]
            ),
            $parameterBuilder
        );
    }
}
