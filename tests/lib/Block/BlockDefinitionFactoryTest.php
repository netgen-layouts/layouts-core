<?php

namespace Netgen\BlockManager\Tests\Block;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\BlockDefinition\ContainerDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinitionFactory;
use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Block\TwigBlockDefinitionInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderFactoryInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Tests\Config\Stubs\Block\HttpCacheConfigHandler;
use Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinition;
use PHPUnit\Framework\TestCase;

class BlockDefinitionFactoryTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $handlerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $parameterBuilderFactoryMock;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionFactory
     */
    protected $factory;

    public function setUp()
    {
        $this->configMock = $this->createMock(Configuration::class);

        $this->parameterBuilderFactoryMock = $this->createMock(ParameterBuilderFactoryInterface::class);
        $this->parameterBuilderFactoryMock
            ->expects($this->any())
            ->method('createParameterBuilder')
            ->will(
                $this->returnValue(
                    $this->createMock(ParameterBuilderInterface::class)
                )
            );

        $this->factory = new BlockDefinitionFactory($this->parameterBuilderFactoryMock);
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::__construct
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::buildBlockDefinition
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::getCommonBlockDefinitionData
     */
    public function testBuildBlockDefinition()
    {
        $this->handlerMock = $this->createMock(BlockDefinitionHandlerInterface::class);

        $blockDefinition = $this->factory->buildBlockDefinition(
            'definition',
            $this->handlerMock,
            $this->configMock,
            array(
                $this->getConfigDefinition('test'),
                $this->getConfigDefinition('test2'),
            )
        );

        $this->assertInstanceOf(BlockDefinitionInterface::class, $blockDefinition);
        $this->assertEquals('definition', $blockDefinition->getIdentifier());
        $this->assertEquals($this->configMock, $blockDefinition->getConfig());

        $this->assertEquals(
            array(
                $this->getConfigDefinition('test'),
                $this->getConfigDefinition('test2'),
            ),
            $blockDefinition->getConfigDefinitions()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::buildTwigBlockDefinition
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::getCommonBlockDefinitionData
     */
    public function testBuildTwigBlockDefinition()
    {
        $this->handlerMock = $this->createMock(TwigBlockDefinitionHandlerInterface::class);

        $blockDefinition = $this->factory->buildTwigBlockDefinition(
            'definition',
            $this->handlerMock,
            $this->configMock,
            array(
                $this->getConfigDefinition('test'),
                $this->getConfigDefinition('test2'),
            )
        );

        $this->assertInstanceOf(TwigBlockDefinitionInterface::class, $blockDefinition);
        $this->assertEquals('definition', $blockDefinition->getIdentifier());
        $this->assertEquals($this->configMock, $blockDefinition->getConfig());

        $this->assertEquals(
            array(
                $this->getConfigDefinition('test'),
                $this->getConfigDefinition('test2'),
            ),
            $blockDefinition->getConfigDefinitions()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::buildContainerDefinition
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::getCommonBlockDefinitionData
     */
    public function testBuildContainerDefinition()
    {
        $this->handlerMock = $this->createMock(ContainerDefinitionHandlerInterface::class);

        $this->handlerMock
            ->expects($this->any())
            ->method('getPlaceholderIdentifiers')
            ->will($this->returnValue(array('left', 'right')));

        $blockDefinition = $this->factory->buildContainerDefinition(
            'definition',
            $this->handlerMock,
            $this->configMock,
            array(
                $this->getConfigDefinition('test'),
                $this->getConfigDefinition('test2'),
            )
        );

        $this->assertInstanceOf(BlockDefinitionInterface::class, $blockDefinition);
        $this->assertEquals('definition', $blockDefinition->getIdentifier());
        $this->assertEquals($this->configMock, $blockDefinition->getConfig());

        $this->assertEquals(
            array(
                $this->getConfigDefinition('test'),
                $this->getConfigDefinition('test2'),
            ),
            $blockDefinition->getConfigDefinitions()
        );

        $this->assertEquals(array('left', 'right'), $blockDefinition->getPlaceholders());
    }

    /**
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\Config\ConfigDefinitionInterface
     */
    protected function getConfigDefinition($identifier)
    {
        $handler = new HttpCacheConfigHandler();

        return new ConfigDefinition('block', $identifier, $handler);
    }
}
