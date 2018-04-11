<?php

namespace Netgen\BlockManager\Tests\Block;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Form;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Block\BlockDefinition\ContainerDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinitionFactory;
use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Block\Registry\HandlerPluginRegistry;
use Netgen\BlockManager\Block\TwigBlockDefinitionInterface;
use Netgen\BlockManager\Config\ConfigDefinitionFactory;
use Netgen\BlockManager\Config\ConfigDefinitionInterface;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Parameters\ParameterBuilderFactory;
use Netgen\BlockManager\Parameters\ParameterType\TextLineType;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry;
use Netgen\BlockManager\Tests\Block\Stubs\HandlerPlugin;
use Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinitionHandler;
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
     * @var \Netgen\BlockManager\Config\ConfigDefinitionFactory
     */
    private $configDefinitionFactory;

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

        $this->configDefinitionFactory = new ConfigDefinitionFactory(
            $this->parameterBuilderFactory
        );

        $this->factory = new BlockDefinitionFactory(
            $this->parameterBuilderFactory,
            $this->handlerPluginRegistry,
            $this->configDefinitionFactory
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::__construct
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::buildBlockDefinition
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::getCommonBlockDefinitionData
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::processConfig
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
                        'name' => 'View type',
                        'item_view_types' => array(
                            'item_view_type' => array(
                                'enabled' => true,
                                'name' => 'Item view type',
                            ),
                            'disabled' => array(
                                'enabled' => false,
                                'name' => 'Item view type',
                            ),
                        ),
                        'valid_parameters' => array(
                            'param1', 'param2',
                        ),
                    ),
                    'disabled' => array(
                        'enabled' => false,
                        'item_view_types' => array(),
                    ),
                ),
                'forms' => array(
                    'form' => array(
                        'enabled' => true,
                        'type' => 'form_type',
                    ),
                    'disabled' => array(
                        'enabled' => false,
                        'type' => 'form_type',
                    ),
                ),
                'collections' => array(
                    'default' => array(
                        'valid_item_types' => null,
                        'valid_query_types' => null,
                    ),
                    'featured' => array(
                        'valid_item_types' => array('item'),
                        'valid_query_types' => array(),
                    ),
                ),
            ),
            array(
                'test' => new ConfigDefinitionHandler(),
                'test2' => new ConfigDefinitionHandler(),
            )
        );

        $this->assertInstanceOf(BlockDefinitionInterface::class, $blockDefinition);
        $this->assertEquals('definition', $blockDefinition->getIdentifier());

        $this->assertArrayHasKey('test_param', $blockDefinition->getParameterDefinitions());
        $this->assertArrayHasKey('dynamic_param', $blockDefinition->getDynamicParameters(new Block()));

        $this->assertEquals(
            array(
                'view_type' => new ViewType(
                    array(
                        'identifier' => 'view_type',
                        'name' => 'View type',
                        'itemViewTypes' => array(
                            'standard' => new ItemViewType(
                                array(
                                    'identifier' => 'standard',
                                    'name' => 'Standard',
                                )
                            ),
                            'item_view_type' => new ItemViewType(
                                array(
                                    'identifier' => 'item_view_type',
                                    'name' => 'Item view type',
                                )
                            ),
                        ),
                        'validParameters' => array('param1', 'param2'),
                    )
                ),
            ),
            $blockDefinition->getViewTypes()
        );

        $this->assertEquals(
            array(
                'form' => new Form(
                    array(
                        'identifier' => 'form',
                        'type' => 'form_type',
                    )
                ),
            ),
            $blockDefinition->getForms()
        );

        $this->assertEquals(
            array(
                'default' => new Collection(
                    array(
                        'identifier' => 'default',
                        'validItemTypes' => null,
                        'validQueryTypes' => null,
                    )
                ),
                'featured' => new Collection(
                    array(
                        'identifier' => 'featured',
                        'validItemTypes' => array('item'),
                        'validQueryTypes' => array(),
                    )
                ),
            ),
            $blockDefinition->getCollections()
        );

        $configDefinitions = $blockDefinition->getConfigDefinitions();
        $this->assertArrayHasKey('test', $configDefinitions);
        $this->assertArrayHasKey('test2', $configDefinitions);

        $this->assertInstanceOf(ConfigDefinitionInterface::class, $configDefinitions['test']);
        $this->assertInstanceOf(ConfigDefinitionInterface::class, $configDefinitions['test2']);
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::buildTwigBlockDefinition
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::getCommonBlockDefinitionData
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::processConfig
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
                'test' => new ConfigDefinitionHandler(),
                'test2' => new ConfigDefinitionHandler(),
            )
        );

        $this->assertInstanceOf(TwigBlockDefinitionInterface::class, $blockDefinition);
        $this->assertEquals('definition', $blockDefinition->getIdentifier());

        $this->assertArrayHasKey('test_param', $blockDefinition->getParameterDefinitions());
        $this->assertArrayHasKey('dynamic_param', $blockDefinition->getDynamicParameters(new Block()));

        $configDefinitions = $blockDefinition->getConfigDefinitions();
        $this->assertArrayHasKey('test', $configDefinitions);
        $this->assertArrayHasKey('test2', $configDefinitions);

        $this->assertInstanceOf(ConfigDefinitionInterface::class, $configDefinitions['test']);
        $this->assertInstanceOf(ConfigDefinitionInterface::class, $configDefinitions['test2']);
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::buildContainerDefinition
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::getCommonBlockDefinitionData
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::processConfig
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
                'test' => new ConfigDefinitionHandler(),
                'test2' => new ConfigDefinitionHandler(),
            )
        );

        $this->assertInstanceOf(BlockDefinitionInterface::class, $blockDefinition);
        $this->assertEquals('definition', $blockDefinition->getIdentifier());

        $this->assertArrayHasKey('test_param', $blockDefinition->getParameterDefinitions());
        $this->assertArrayHasKey('dynamic_param', $blockDefinition->getDynamicParameters(new Block()));

        $configDefinitions = $blockDefinition->getConfigDefinitions();
        $this->assertArrayHasKey('test', $configDefinitions);
        $this->assertArrayHasKey('test2', $configDefinitions);

        $this->assertInstanceOf(ConfigDefinitionInterface::class, $configDefinitions['test']);
        $this->assertInstanceOf(ConfigDefinitionInterface::class, $configDefinitions['test2']);

        $this->assertEquals(array('left', 'right'), $blockDefinition->getPlaceholders());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::buildBlockDefinition
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::getCommonBlockDefinitionData
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::processConfig
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
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::getCommonBlockDefinitionData
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::processConfig
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
}
