<?php

namespace Netgen\BlockManager\Tests\Layout\Container\ContainerDefinition;

use Netgen\BlockManager\Layout\Container\ContainerDefinition\ContainerDefinitionHandler;
use Netgen\BlockManager\Parameters\ParameterBuilder;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry;
use PHPUnit\Framework\TestCase;

class ContainerDefinitionHandlerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Container\ContainerDefinition\ContainerDefinitionHandler
     */
    protected $handler;

    /**
     * @var \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistryInterface
     */
    protected $parameterTypeRegistry;

    public function setUp()
    {
        $this->handler = $this->getMockForAbstractClass(ContainerDefinitionHandler::class);

        $this->parameterTypeRegistry = new ParameterTypeRegistry();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\ContainerDefinition\ContainerDefinitionHandler::buildParameters
     */
    public function testBuildParameters()
    {
        $builder = new ParameterBuilder($this->parameterTypeRegistry);

        $this->handler->buildParameters($builder);

        $this->assertCount(0, $builder);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\ContainerDefinition\ContainerDefinitionHandler::buildPlaceholderParameters
     */
    public function testBuildPlaceholderParameters()
    {
        $builder = new ParameterBuilder($this->parameterTypeRegistry);

        $this->handler->buildPlaceholderParameters(array('left' => $builder));

        $this->assertCount(0, $builder);
    }
}
