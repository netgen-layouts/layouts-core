<?php

namespace Netgen\BlockManager\Tests\Config;

use Netgen\BlockManager\Config\ConfigDefinition\ConfigDefinitionHandlerInterface;
use Netgen\BlockManager\Config\ConfigDefinitionFactory;
use Netgen\BlockManager\Config\ConfigDefinitionInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use PHPUnit\Framework\TestCase;

class ConfigDefinitionFactoryTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $handlerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $parameterBuilderMock;

    /**
     * @var \Netgen\BlockManager\Config\ConfigDefinitionFactory
     */
    protected $factory;

    public function setUp()
    {
        $this->parameterBuilderMock = $this->createMock(ParameterBuilderInterface::class);

        $this->factory = new ConfigDefinitionFactory();
    }

    /**
     * @covers \Netgen\BlockManager\Config\ConfigDefinitionFactory::__construct
     * @covers \Netgen\BlockManager\Config\ConfigDefinitionFactory::buildConfigDefinition
     */
    public function testBuildConfigDefinition()
    {
        $this->handlerMock = $this->createMock(ConfigDefinitionHandlerInterface::class);

        $configDefinition = $this->factory->buildConfigDefinition(
            'type',
            'definition',
            $this->handlerMock,
            $this->parameterBuilderMock
        );

        $this->assertInstanceOf(ConfigDefinitionInterface::class, $configDefinition);
        $this->assertEquals('type', $configDefinition->getType());
        $this->assertEquals('definition', $configDefinition->getIdentifier());
    }
}
