<?php

namespace Netgen\BlockManager\Tests\Config;

use Netgen\BlockManager\Config\ConfigDefinitionFactory;
use Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface;
use Netgen\BlockManager\Config\ConfigDefinitionInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderFactoryInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use PHPUnit\Framework\TestCase;

class ConfigDefinitionFactoryTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $handlerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $parameterBuilderFactoryMock;

    /**
     * @var \Netgen\BlockManager\Config\ConfigDefinitionFactory
     */
    private $factory;

    public function setUp()
    {
        $this->parameterBuilderFactoryMock = $this->createMock(ParameterBuilderFactoryInterface::class);
        $this->parameterBuilderFactoryMock
            ->expects($this->any())
            ->method('createParameterBuilder')
            ->will(
                $this->returnValue(
                    $this->createMock(ParameterBuilderInterface::class)
                )
            );

        $this->factory = new ConfigDefinitionFactory($this->parameterBuilderFactoryMock);
    }

    /**
     * @covers \Netgen\BlockManager\Config\ConfigDefinitionFactory::__construct
     * @covers \Netgen\BlockManager\Config\ConfigDefinitionFactory::buildConfigDefinition
     */
    public function testBuildConfigDefinition()
    {
        $this->handlerMock = $this->createMock(ConfigDefinitionHandlerInterface::class);

        $configDefinition = $this->factory->buildConfigDefinition(
            'definition',
            $this->handlerMock
        );

        $this->assertInstanceOf(ConfigDefinitionInterface::class, $configDefinition);
        $this->assertEquals('definition', $configDefinition->getConfigKey());
    }
}
