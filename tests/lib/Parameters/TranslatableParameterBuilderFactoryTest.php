<?php

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry;
use Netgen\BlockManager\Parameters\TranslatableParameterBuilder;
use Netgen\BlockManager\Parameters\TranslatableParameterBuilderFactory;
use PHPUnit\Framework\TestCase;

class TranslatableParameterBuilderFactoryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistryInterface
     */
    protected $registry;

    /**
     * @var \Netgen\BlockManager\Parameters\TranslatableParameterBuilderFactory
     */
    protected $factory;

    public function setUp()
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
    public function testCreateParameterBuilder()
    {
        $parameterBuilder = $this->factory->createParameterBuilder();

        $this->assertEquals(new TranslatableParameterBuilder($this->factory), $parameterBuilder);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\TranslatableParameterBuilderFactory::createParameterBuilder
     * @covers \Netgen\BlockManager\Parameters\TranslatableParameterBuilderFactory::resolveOptions
     */
    public function testCreateParameterBuilderWithNoOptions()
    {
        $parameterBuilder = $this->factory->createParameterBuilder(
            array(
                'name' => 'param',
                'type' => ParameterType\TextType::class,
            )
        );

        $this->assertEquals(
            new TranslatableParameterBuilder(
                $this->factory,
                'param',
                $this->registry->getParameterTypeByClass(ParameterType\TextType::class),
                array(
                    'translatable' => true,
                )
            ),
            $parameterBuilder
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\TranslatableParameterBuilderFactory::createParameterBuilder
     * @covers \Netgen\BlockManager\Parameters\TranslatableParameterBuilderFactory::resolveOptions
     */
    public function testCreateParameterBuilderWithConfig()
    {
        $parameterBuilder = $this->factory->createParameterBuilder(
            array(
                'name' => 'param',
                'type' => ParameterType\TextType::class,
                'options' => array(
                    'translatable' => false,
                ),
            )
        );

        $this->assertEquals(
            new TranslatableParameterBuilder(
                $this->factory,
                'param',
                $this->registry->getParameterTypeByClass(ParameterType\TextType::class),
                array(
                    'translatable' => false,
                )
            ),
            $parameterBuilder
        );
    }
}
