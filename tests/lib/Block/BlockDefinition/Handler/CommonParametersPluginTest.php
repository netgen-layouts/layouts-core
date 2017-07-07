<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Handler;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\Handler\CommonParametersPlugin;
use Netgen\BlockManager\Parameters\ParameterBuilderFactory;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry;
use PHPUnit\Framework\TestCase;

class CommonParametersPluginTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Handler\CommonParametersPlugin
     */
    protected $plugin;

    /**
     * @var \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistryInterface
     */
    protected $parameterTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterBuilderFactoryInterface
     */
    protected $parameterBuilderFactory;

    public function setUp()
    {
        $this->plugin = new CommonParametersPlugin(array('group'));

        $this->parameterTypeRegistry = new ParameterTypeRegistry();
        $this->parameterTypeRegistry->addParameterType(new ParameterType\TextLineType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\BooleanType());

        $this->parameterBuilderFactory = new ParameterBuilderFactory(
            $this->parameterTypeRegistry
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\CommonParametersPlugin::__construct
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\CommonParametersPlugin::getExtendedHandler
     */
    public function testGetExtendedHandler()
    {
        $plugin = $this->plugin;

        $this->assertEquals(BlockDefinitionHandlerInterface::class, $plugin::getExtendedHandler());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\CommonParametersPlugin::buildParameters
     */
    public function testBuildParameters()
    {
        $builder = $this->parameterBuilderFactory->createParameterBuilder();
        $this->plugin->buildParameters($builder);

        $this->assertCount(3, $builder);

        $this->assertTrue($builder->has('css_class'));
        $this->assertInstanceOf(ParameterType\TextLineType::class, $builder->get('css_class')->getType());
        $this->assertEquals(array('group'), $builder->get('css_class')->getGroups());

        $this->assertTrue($builder->has('css_id'));
        $this->assertInstanceOf(ParameterType\TextLineType::class, $builder->get('css_id')->getType());
        $this->assertEquals(array('group'), $builder->get('css_id')->getGroups());

        $this->assertTrue($builder->has('set_container'));
        $this->assertInstanceOf(ParameterType\BooleanType::class, $builder->get('set_container')->getType());
        $this->assertEquals(array('group'), $builder->get('set_container')->getGroups());
    }
}
