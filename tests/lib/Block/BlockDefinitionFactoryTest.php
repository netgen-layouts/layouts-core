<?php

namespace Netgen\BlockManager\Tests\Block;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\BlockDefinition\ContainerDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinitionFactory;
use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Block\Registry\HandlerPluginRegistry;
use Netgen\BlockManager\Block\TwigBlockDefinitionInterface;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Parameters\ParameterBuilderFactory;
use Netgen\BlockManager\Parameters\ParameterType\TextLineType;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry;
use Netgen\BlockManager\Tests\Block\Stubs\HandlerPlugin;
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
     * @var \Netgen\BlockManager\Parameters\ParameterBuilderFactoryInterface
     */
    protected $parameterBuilderFactory;

    /**
     * @var \Netgen\BlockManager\Block\Registry\HandlerPluginRegistryInterface
     */
    protected $handlerPluginRegistry;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionFactory
     */
    protected $factory;

    public function setUp()
    {
        $this->configMock = $this->createMock(Configuration::class);

        $parameterTypeRegistry = new ParameterTypeRegistry();
        $parameterTypeRegistry->addParameterType(new TextLineType());
        $this->parameterBuilderFactory = new ParameterBuilderFactory(
            $parameterTypeRegistry
        );

        $this->handlerPluginRegistry = new HandlerPluginRegistry();
        $this->handlerPluginRegistry->addPlugin(new HandlerPlugin());

        $this->factory = new BlockDefinitionFactory(
            $this->parameterBuilderFactory,
            $this->handlerPluginRegistry
        );
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

        $this->assertArrayHasKey('test_param', $blockDefinition->getParameters());
        $this->assertArrayHasKey('dynamic_param', $blockDefinition->getDynamicParameters(new Block()));

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

        $this->assertArrayHasKey('test_param', $blockDefinition->getParameters());
        $this->assertArrayHasKey('dynamic_param', $blockDefinition->getDynamicParameters(new Block()));

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

        $this->assertArrayHasKey('test_param', $blockDefinition->getParameters());
        $this->assertArrayHasKey('dynamic_param', $blockDefinition->getDynamicParameters(new Block()));

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

        return new ConfigDefinition($identifier, $handler);
    }
}
