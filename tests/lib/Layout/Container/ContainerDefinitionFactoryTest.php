<?php

namespace Netgen\BlockManager\Tests\Layout\Container;

use Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Configuration;
use Netgen\BlockManager\Layout\Container\ContainerDefinitionFactory;
use Netgen\BlockManager\Layout\Container\ContainerDefinitionInterface;
use Netgen\BlockManager\Layout\Container\DynamicContainerDefinitionInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Tests\Layout\Container\Stubs\ContainerDefinitionHandler;
use Netgen\BlockManager\Tests\Layout\Container\Stubs\DynamicContainerDefinitionHandler;
use PHPUnit\Framework\TestCase;

class ContainerDefinitionFactoryTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $parameterBuilderMock;

    public function setUp()
    {
        $this->configMock = $this->createMock(Configuration::class);
        $this->parameterBuilderMock = $this->createMock(ParameterBuilderInterface::class);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\ContainerDefinitionFactory::buildContainerDefinition
     */
    public function testBuildContainerDefinition()
    {
        $containerDefinition = ContainerDefinitionFactory::buildContainerDefinition(
            'definition',
            new ContainerDefinitionHandler(),
            $this->configMock,
            $this->parameterBuilderMock
        );

        $this->assertInstanceOf(ContainerDefinitionInterface::class, $containerDefinition);
        $this->assertEquals('definition', $containerDefinition->getIdentifier());
        $this->assertEquals($this->configMock, $containerDefinition->getConfig());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\ContainerDefinitionFactory::buildDynamicContainerDefinition
     */
    public function testBuildDynamicContainerDefinition()
    {
        $containerDefinition = ContainerDefinitionFactory::buildDynamicContainerDefinition(
            'definition',
            new DynamicContainerDefinitionHandler(),
            $this->configMock,
            $this->parameterBuilderMock
        );

        $this->assertInstanceOf(DynamicContainerDefinitionInterface::class, $containerDefinition);
        $this->assertEquals('definition', $containerDefinition->getIdentifier());
        $this->assertEquals($this->configMock, $containerDefinition->getConfig());
    }
}
