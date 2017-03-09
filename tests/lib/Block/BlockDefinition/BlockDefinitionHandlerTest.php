<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Parameters\ParameterBuilder;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry;
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

    public function setUp()
    {
        $this->handler = $this->getMockForAbstractClass(BlockDefinitionHandler::class);

        $this->parameterTypeRegistry = new ParameterTypeRegistry();
        $this->parameterTypeRegistry->addParameterType(new ParameterType\TextLineType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\BooleanType());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler::buildParameters
     * @covers \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler::buildCommonParameters
     */
    public function testBuildParameters()
    {
        $builder = new ParameterBuilder($this->parameterTypeRegistry);

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
     * @covers \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler::buildPlaceholderParameters
     */
    public function testBuildPlaceholderParameters()
    {
        $builder = new ParameterBuilder($this->parameterTypeRegistry);

        $this->handler->buildPlaceholderParameters(array('left' => $builder));

        $this->assertCount(0, $builder);
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler::buildDynamicPlaceholderParameters
     */
    public function testBuildDynamicPlaceholderParameters()
    {
        $builder = new ParameterBuilder($this->parameterTypeRegistry);

        $this->handler->buildDynamicPlaceholderParameters($builder);

        $this->assertCount(0, $builder);
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler::getDynamicParameters
     */
    public function testGetDynamicParameters()
    {
        $this->assertEquals(array(), $this->handler->getDynamicParameters(new Block()));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler::hasCollection
     */
    public function testHasCollection()
    {
        $this->assertFalse($this->handler->hasCollection());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler::isContainer
     */
    public function testIsContainer()
    {
        $this->assertFalse($this->handler->isContainer());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler::isDynamicContainer
     */
    public function testIsDynamicContainer()
    {
        $this->assertFalse($this->handler->isDynamicContainer());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler::getPlaceholderIdentifiers
     */
    public function testGetPlaceholderIdentifiers()
    {
        $this->assertEquals(array(), $this->handler->getPlaceholderIdentifiers());
    }
}
