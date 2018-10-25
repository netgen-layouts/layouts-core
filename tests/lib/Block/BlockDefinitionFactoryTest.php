<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\ContainerDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinitionFactory;
use Netgen\BlockManager\Block\Registry\HandlerPluginRegistry;
use Netgen\BlockManager\Config\ConfigDefinitionFactory;
use Netgen\BlockManager\Config\ConfigDefinitionInterface;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Parameters\ParameterBuilderFactory;
use Netgen\BlockManager\Parameters\ParameterType\TextLineType;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry;
use Netgen\BlockManager\Tests\Block\Stubs\HandlerPlugin;
use Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinitionHandler;
use Netgen\BlockManager\Tests\TestCase\ExportObjectTrait;
use PHPUnit\Framework\TestCase;

final class BlockDefinitionFactoryTest extends TestCase
{
    use ExportObjectTrait;

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

    public function setUp(): void
    {
        $parameterTypeRegistry = new ParameterTypeRegistry(new TextLineType());
        $this->parameterBuilderFactory = new ParameterBuilderFactory(
            $parameterTypeRegistry
        );

        $this->handlerPluginRegistry = new HandlerPluginRegistry(
            [
                HandlerPlugin::instance([BlockDefinitionHandlerInterface::class]),
            ]
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
    public function testBuildBlockDefinition(): void
    {
        $this->handlerMock = $this->createMock(BlockDefinitionHandlerInterface::class);

        $blockDefinition = $this->factory->buildBlockDefinition(
            'definition',
            $this->handlerMock,
            [
                'view_types' => [
                    'disabled' => [
                        'enabled' => false,
                        'item_view_types' => [],
                    ],
                    'view_type' => [
                        'enabled' => true,
                        'name' => 'View type',
                        'item_view_types' => [
                            'disabled' => [
                                'enabled' => false,
                                'name' => 'Item view type',
                            ],
                            'item_view_type' => [
                                'enabled' => true,
                                'name' => 'Item view type',
                            ],
                        ],
                        'valid_parameters' => [
                            'param1', 'param2',
                        ],
                    ],
                ],
                'forms' => [
                    'disabled' => [
                        'enabled' => false,
                        'type' => 'form_type',
                    ],
                    'form' => [
                        'enabled' => true,
                        'type' => 'form_type',
                    ],
                ],
                'collections' => [
                    'default' => [
                        'valid_item_types' => null,
                        'valid_query_types' => null,
                    ],
                    'featured' => [
                        'valid_item_types' => ['item'],
                        'valid_query_types' => [],
                    ],
                ],
            ],
            [
                'test' => new ConfigDefinitionHandler(),
                'test2' => new ConfigDefinitionHandler(),
            ]
        );

        self::assertSame('definition', $blockDefinition->getIdentifier());
        self::assertFalse($blockDefinition->isTranslatable());

        self::assertArrayHasKey('test_param', $blockDefinition->getParameterDefinitions());
        self::assertArrayHasKey('dynamic_param', $blockDefinition->getDynamicParameters(new Block()));

        self::assertTrue($blockDefinition->hasViewType('view_type'));

        $viewType = $blockDefinition->getViewType('view_type');
        self::assertTrue($viewType->hasItemViewType('standard'));
        self::assertTrue($viewType->hasItemViewType('item_view_type'));

        self::assertSame(
            [
                'view_type' => [
                    'identifier' => 'view_type',
                    'name' => 'View type',
                    'itemViewTypes' => [
                        'item_view_type' => [
                            'identifier' => 'item_view_type',
                            'name' => 'Item view type',
                        ],
                        'standard' => [
                            'identifier' => 'standard',
                            'name' => 'Standard',
                        ],
                    ],
                    'validParameters' => ['param1', 'param2'],
                ],
            ],
            $this->exportObjectList($blockDefinition->getViewTypes(), true)
        );

        self::assertTrue($blockDefinition->hasForm('form'));

        self::assertSame(
            [
                'identifier' => 'form',
                'type' => 'form_type',
            ],
            $this->exportObject($blockDefinition->getForm('form'))
        );

        self::assertTrue($blockDefinition->hasCollection('default'));
        self::assertTrue($blockDefinition->hasCollection('featured'));

        self::assertSame(
            [
                'default' => [
                    'identifier' => 'default',
                    'validItemTypes' => null,
                    'validQueryTypes' => null,
                ],
                'featured' => [
                    'identifier' => 'featured',
                    'validItemTypes' => ['item'],
                    'validQueryTypes' => [],
                ],
            ],
            $this->exportObjectList($blockDefinition->getCollections())
        );

        $configDefinitions = $blockDefinition->getConfigDefinitions();
        self::assertArrayHasKey('test', $configDefinitions);
        self::assertArrayHasKey('test2', $configDefinitions);
        self::assertContainsOnlyInstancesOf(ConfigDefinitionInterface::class, $configDefinitions);
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::buildTwigBlockDefinition
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::getCommonBlockDefinitionData
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::processConfig
     */
    public function testBuildTwigBlockDefinition(): void
    {
        $this->handlerMock = $this->createMock(TwigBlockDefinitionHandlerInterface::class);

        $blockDefinition = $this->factory->buildTwigBlockDefinition(
            'definition',
            $this->handlerMock,
            [
                'translatable' => true,
                'view_types' => [
                    'view_type' => [
                        'enabled' => true,
                        'item_view_types' => [],
                    ],
                ],
            ],
            [
                'test' => new ConfigDefinitionHandler(),
                'test2' => new ConfigDefinitionHandler(),
            ]
        );

        self::assertSame('definition', $blockDefinition->getIdentifier());
        self::assertTrue($blockDefinition->isTranslatable());

        self::assertArrayHasKey('test_param', $blockDefinition->getParameterDefinitions());
        self::assertArrayHasKey('dynamic_param', $blockDefinition->getDynamicParameters(new Block()));

        $configDefinitions = $blockDefinition->getConfigDefinitions();
        self::assertArrayHasKey('test', $configDefinitions);
        self::assertArrayHasKey('test2', $configDefinitions);

        self::assertContainsOnlyInstancesOf(ConfigDefinitionInterface::class, $configDefinitions);
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::buildContainerDefinition
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::getCommonBlockDefinitionData
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::processConfig
     */
    public function testBuildContainerDefinition(): void
    {
        $this->handlerMock = $this->createMock(ContainerDefinitionHandlerInterface::class);

        $this->handlerMock
            ->expects(self::any())
            ->method('getPlaceholderIdentifiers')
            ->will(self::returnValue(['left', 'right']));

        $blockDefinition = $this->factory->buildContainerDefinition(
            'definition',
            $this->handlerMock,
            [
                'translatable' => false,
                'view_types' => [
                    'view_type' => [
                        'enabled' => true,
                        'item_view_types' => [],
                    ],
                ],
            ],
            [
                'test' => new ConfigDefinitionHandler(),
                'test2' => new ConfigDefinitionHandler(),
            ]
        );

        self::assertSame('definition', $blockDefinition->getIdentifier());
        self::assertFalse($blockDefinition->isTranslatable());

        self::assertArrayHasKey('test_param', $blockDefinition->getParameterDefinitions());
        self::assertArrayHasKey('dynamic_param', $blockDefinition->getDynamicParameters(new Block()));

        $configDefinitions = $blockDefinition->getConfigDefinitions();
        self::assertArrayHasKey('test', $configDefinitions);
        self::assertArrayHasKey('test2', $configDefinitions);
        self::assertContainsOnlyInstancesOf(ConfigDefinitionInterface::class, $configDefinitions);

        self::assertSame(['left', 'right'], $blockDefinition->getPlaceholders());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::buildBlockDefinition
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::getCommonBlockDefinitionData
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::processConfig
     */
    public function testBuildConfigWithNoViewTypes(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('You need to specify at least one enabled view type for "definition" block definition.');

        $this->handlerMock = $this->createMock(BlockDefinitionHandlerInterface::class);

        $this->factory->buildBlockDefinition(
            'definition',
            $this->handlerMock,
            [
                'view_types' => [
                    'large' => [
                        'enabled' => false,
                        'valid_parameters' => null,
                    ],
                ],
            ],
            []
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::buildBlockDefinition
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::getCommonBlockDefinitionData
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::processConfig
     */
    public function testBuildConfigWithNoItemViewTypes(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('You need to specify at least one enabled item view type for "large" view type and "definition" block definition.');

        $this->handlerMock = $this->createMock(BlockDefinitionHandlerInterface::class);

        $this->factory->buildBlockDefinition(
            'definition',
            $this->handlerMock,
            [
                'view_types' => [
                    'large' => [
                        'name' => 'Large',
                        'enabled' => true,
                        'item_view_types' => [
                            'standard' => [
                                'enabled' => false,
                            ],
                        ],
                        'valid_parameters' => null,
                    ],
                ],
            ],
            []
        );
    }
}
