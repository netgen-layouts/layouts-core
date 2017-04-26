<?php

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Parameters\ParameterBuilder;
use Netgen\BlockManager\Parameters\ParameterBuilderFactory;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry;
use PHPUnit\Framework\TestCase;

class ParameterBuilderFactoryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistryInterface
     */
    protected $registry;

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterBuilderFactory
     */
    protected $factory;

    public function setUp()
    {
        $this->registry = new ParameterTypeRegistry();
        $this->registry->addParameterType(new ParameterType\TextType());
        $this->registry->addParameterType(new ParameterType\Compound\BooleanType());

        $this->factory = new ParameterBuilderFactory($this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilderFactory::__construct
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilderFactory::createParameterBuilder
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilderFactory::resolveOptions
     */
    public function testCreateParameterBuilder()
    {
        $parameterBuilder = $this->factory->createParameterBuilder();

        $this->assertEquals(new ParameterBuilder($this->factory), $parameterBuilder);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilderFactory::createParameterBuilder
     * @covers \Netgen\BlockManager\Parameters\ParameterBuilderFactory::resolveOptions
     */
    public function testCreateParameterBuilderWithConfig()
    {
        $parameterBuilder = $this->factory->createParameterBuilder(
            array(
                'name' => 'param',
                'type' => ParameterType\TextType::class,
            )
        );

        $this->assertEquals(
            new ParameterBuilder(
                $this->factory,
                'param',
                $this->registry->getParameterTypeByClass(ParameterType\TextType::class)
            ),
            $parameterBuilder
        );
    }
}
