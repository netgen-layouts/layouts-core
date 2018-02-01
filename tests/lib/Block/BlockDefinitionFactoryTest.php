<?php

namespace Netgen\BlockManager\Tests\Block;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
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

final class BlockDefinitionFactoryTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $handlerMock;

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterBuilderFactoryInterface
     */
    private $parameterBuilderFactory;

    /**
     * @var \Netgen\BlockManager\Block\Registry\HandlerPluginRegistryInterface
     */
    private $handlerPluginRegistry;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionFactory
     */
    private $factory;

    public function setUp()
    {
        $parameterTypeRegistry = new ParameterTypeRegistry();
        $parameterTypeRegistry->addParameterType(new TextLineType());
        $this->parameterBuilderFactory = new ParameterBuilderFactory(
            $parameterTypeRegistry
        );

        $this->handlerPluginRegistry = new HandlerPluginRegistry();
        $this->handlerPluginRegistry->addPlugin(
            HandlerPlugin::instance(
                array(BlockDefinitionHandlerInterface::class)
            )
        );

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
            array(
                'view_types' => array(
                    'view_type' => array(
                        'enabled' => true,
                        'item_view_types' => array(),
                    ),
                ),
            ),
            array(
                $this->getConfigDefinition('test'),
                $this->getConfigDefinition('test2'),
            )
        );

        $this->assertInstanceOf(BlockDefinitionInterface::class, $blockDefinition);
        $this->assertEquals('definition', $blockDefinition->getIdentifier());

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
            array(
                'view_types' => array(
                    'view_type' => array(
                        'enabled' => true,
                        'item_view_types' => array(),
                    ),
                ),
            ),
            array(
                $this->getConfigDefinition('test'),
                $this->getConfigDefinition('test2'),
            )
        );

        $this->assertInstanceOf(TwigBlockDefinitionInterface::class, $blockDefinition);
        $this->assertEquals('definition', $blockDefinition->getIdentifier());

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
            array(
                'view_types' => array(
                    'view_type' => array(
                        'enabled' => true,
                        'item_view_types' => array(),
                    ),
                ),
            ),
            array(
                $this->getConfigDefinition('test'),
                $this->getConfigDefinition('test2'),
            )
        );

        $this->assertInstanceOf(BlockDefinitionInterface::class, $blockDefinition);
        $this->assertEquals('definition', $blockDefinition->getIdentifier());

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
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::buildBlockDefinition
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage You need to specify at least one enabled view type for "definition" block definition.
     */
    public function testBuildConfigWithNoViewTypes()
    {
        $this->handlerMock = $this->createMock(BlockDefinitionHandlerInterface::class);

        $this->factory->buildBlockDefinition(
            'definition',
            $this->handlerMock,
            array(
                'view_types' => array(
                    'large' => array(
                        'enabled' => false,
                        'valid_parameters' => null,
                    ),
                ),
            ),
            array()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::buildBlockDefinition
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage You need to specify at least one enabled item view type for "large" view type and "definition" block definition.
     */
    public function testBuildConfigWithNoItemViewTypes()
    {
        $this->handlerMock = $this->createMock(BlockDefinitionHandlerInterface::class);

        $this->factory->buildBlockDefinition(
            'definition',
            $this->handlerMock,
            array(
                'view_types' => array(
                    'large' => array(
                        'name' => 'Large',
                        'enabled' => true,
                        'item_view_types' => array(
                            'standard' => array(
                                'enabled' => false,
                            ),
                        ),
                        'valid_parameters' => null,
                    ),
                ),
            ),
            array()
        );
    }

    /**
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\Config\ConfigDefinitionInterface
     */
    private function getConfigDefinition($identifier)
    {
        $handler = new HttpCacheConfigHandler();

        return new ConfigDefinition($identifier, $handler);
    }
}
