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
        $this->assertEquals(false, $this->handler->hasCollection());
    }
}
