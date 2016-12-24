<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Parameters\ParameterBuilder;
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
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler::buildParameters
     */
    public function testBuildParameters()
    {
        $builder = new ParameterBuilder($this->parameterTypeRegistry);

        $this->handler->buildParameters($builder);

        $this->assertCount(0, $builder);
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
