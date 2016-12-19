<?php

namespace Netgen\BlockManager\Tests\Layout\Container\ContainerDefinition;

use Netgen\BlockManager\Layout\Container\ContainerDefinition\DynamicContainerDefinitionHandler;
use Netgen\BlockManager\Parameters\ParameterBuilder;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry;
use PHPUnit\Framework\TestCase;

class DynamicContainerDefinitionHandlerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Container\ContainerDefinition\DynamicContainerDefinitionHandler
     */
    protected $handler;

    /**
     * @var \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistryInterface
     */
    protected $parameterTypeRegistry;

    public function setUp()
    {
        $this->handler = $this->getMockForAbstractClass(DynamicContainerDefinitionHandler::class);

        $this->parameterTypeRegistry = new ParameterTypeRegistry();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\ContainerDefinition\DynamicContainerDefinitionHandler::buildDynamicPlaceholderParameters
     */
    public function testBuildDynamicPlaceholderParameters()
    {
        $builder = new ParameterBuilder($this->parameterTypeRegistry);

        $this->handler->buildDynamicPlaceholderParameters($builder);

        $this->assertCount(0, $builder);
    }
}
