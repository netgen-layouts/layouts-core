<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Handler;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\Handler\CommonParametersPlugin;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry;
use Netgen\BlockManager\Parameters\TranslatableParameterBuilderFactory;
use PHPUnit\Framework\TestCase;

final class CommonParametersPluginTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Handler\CommonParametersPlugin
     */
    private $plugin;

    /**
     * @var \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistryInterface
     */
    private $parameterTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterBuilderFactoryInterface
     */
    private $parameterBuilderFactory;

    public function setUp()
    {
        $this->plugin = new CommonParametersPlugin(['group']);

        $this->parameterTypeRegistry = new ParameterTypeRegistry();
        $this->parameterTypeRegistry->addParameterType(new ParameterType\TextLineType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\BooleanType());

        $this->parameterBuilderFactory = new TranslatableParameterBuilderFactory(
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
        $this->assertEquals(['group'], $builder->get('css_class')->getGroups());
        $this->assertFalse($builder->get('css_class')->getOption('translatable'));

        $this->assertTrue($builder->has('css_id'));
        $this->assertInstanceOf(ParameterType\TextLineType::class, $builder->get('css_id')->getType());
        $this->assertEquals(['group'], $builder->get('css_id')->getGroups());
        $this->assertFalse($builder->get('css_id')->getOption('translatable'));

        $this->assertTrue($builder->has('set_container'));
        $this->assertInstanceOf(ParameterType\BooleanType::class, $builder->get('set_container')->getType());
        $this->assertEquals(['group'], $builder->get('set_container')->getGroups());
        $this->assertFalse($builder->get('set_container')->getOption('translatable'));
    }
}
