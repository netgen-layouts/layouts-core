<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry;
use Netgen\BlockManager\Parameters\ParameterBuilderFactory;
use PHPUnit\Framework\TestCase;

class BlockDefinitionHandlerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler
     */
    protected $handler;

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
        $this->handler = $this->getMockForAbstractClass(BlockDefinitionHandler::class);

        $this->parameterTypeRegistry = new ParameterTypeRegistry();
        $this->parameterTypeRegistry->addParameterType(new ParameterType\TextLineType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\BooleanType());

        $this->parameterBuilderFactory = new ParameterBuilderFactory(
            $this->parameterTypeRegistry
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler::buildParameters
     * @covers \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler::buildCommonParameters
     */
    public function testBuildParameters()
    {
        $builder = $this->parameterBuilderFactory->createParameterBuilder();
        $this->handler->buildParameters($builder);

        $this->assertCount(3, $builder);

        $this->assertTrue($builder->has('css_class'));
        $this->assertInstanceOf(ParameterType\TextLineType::class, $builder->get('css_class')->getType());

        $this->assertTrue($builder->has('css_id'));
        $this->assertInstanceOf(ParameterType\TextLineType::class, $builder->get('css_id')->getType());

        $this->assertTrue($builder->has('set_container'));
        $this->assertInstanceOf(ParameterType\BooleanType::class, $builder->get('set_container')->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler::getDynamicParameters
     */
    public function testGetDynamicParameters()
    {
        $this->assertEquals(array(), $this->handler->getDynamicParameters(new Block()));
    }
}
