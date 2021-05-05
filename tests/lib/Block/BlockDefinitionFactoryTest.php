<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\Layouts\Block\BlockDefinition\ContainerDefinitionHandlerInterface;
use Netgen\Layouts\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface;
use Netgen\Layouts\Block\BlockDefinitionFactory;
use Netgen\Layouts\Block\Registry\HandlerPluginRegistry;
use Netgen\Layouts\Config\ConfigDefinitionFactory;
use Netgen\Layouts\Config\ConfigDefinitionInterface;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Parameters\ParameterBuilderFactory;
use Netgen\Layouts\Parameters\ParameterType\TextLineType;
use Netgen\Layouts\Parameters\Registry\ParameterTypeRegistry;
use Netgen\Layouts\Tests\Block\Stubs\HandlerPlugin;
use Netgen\Layouts\Tests\Config\Stubs\ConfigDefinitionHandler;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class BlockDefinitionFactoryTest extends TestCase
{
    use ExportObjectTrait;

    private MockObject $handlerMock;

    private ParameterBuilderFactory $parameterBuilderFactory;

    private HandlerPluginRegistry $handlerPluginRegistry;

    private ConfigDefinitionFactory $configDefinitionFactory;

    private BlockDefinitionFactory $factory;

    protected function setUp(): void
    {
        $parameterTypeRegistry = new ParameterTypeRegistry([new TextLineType()]);
        $this->parameterBuilderFactory = new ParameterBuilderFactory(
            $parameterTypeRegistry,
        );

        $this->handlerPluginRegistry = new HandlerPluginRegistry(
            [
                HandlerPlugin::instance([BlockDefinitionHandlerInterface::class]),
            ],
        );

        $this->configDefinitionFactory = new ConfigDefinitionFactory(
            $this->parameterBuilderFactory,
        );

        $this->factory = new BlockDefinitionFactory(
            $this->parameterBuilderFactory,
            $this->handlerPluginRegistry,
            $this->configDefinitionFactory,
        );
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinitionFactory::__construct
     * @covers \Netgen\Layouts\Block\BlockDefinitionFactory::buildBlockDefinition
     * @covers \Netgen\Layouts\Block\BlockDefinitionFactory::getCommonBlockDefinitionData
     * @covers \Netgen\Layouts\Block\BlockDefinitionFactory::processConfig
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
            ],
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
            $this->exportObjectList($blockDefinition->getViewTypes(), true),
        );

        self::assertTrue($blockDefinition->hasForm('form'));

        self::assertSame(
            [
                'identifier' => 'form',
                'type' => 'form_type',
            ],
            $this->exportObject($blockDefinition->getForm('form')),
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
            $this->exportObjectList($blockDefinition->getCollections()),
        );

        $configDefinitions = $blockDefinition->getConfigDefinitions();
        self::assertArrayHasKey('test', $configDefinitions);
        self::assertArrayHasKey('test2', $configDefinitions);
        self::assertContainsOnlyInstancesOf(ConfigDefinitionInterface::class, $configDefinitions);
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinitionFactory::buildTwigBlockDefinition
     * @covers \Netgen\Layouts\Block\BlockDefinitionFactory::getCommonBlockDefinitionData
     * @covers \Netgen\Layouts\Block\BlockDefinitionFactory::processConfig
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
            ],
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
     * @covers \Netgen\Layouts\Block\BlockDefinitionFactory::buildContainerDefinition
     * @covers \Netgen\Layouts\Block\BlockDefinitionFactory::getCommonBlockDefinitionData
     * @covers \Netgen\Layouts\Block\BlockDefinitionFactory::processConfig
     */
    public function testBuildContainerDefinition(): void
    {
        $this->handlerMock = $this->createMock(ContainerDefinitionHandlerInterface::class);

        $this->handlerMock
            ->expects(self::any())
            ->method('getPlaceholderIdentifiers')
            ->willReturn(['left', 'right']);

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
            ],
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
     * @covers \Netgen\Layouts\Block\BlockDefinitionFactory::buildBlockDefinition
     * @covers \Netgen\Layouts\Block\BlockDefinitionFactory::getCommonBlockDefinitionData
     * @covers \Netgen\Layouts\Block\BlockDefinitionFactory::processConfig
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
            [],
        );
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinitionFactory::buildBlockDefinition
     * @covers \Netgen\Layouts\Block\BlockDefinitionFactory::getCommonBlockDefinitionData
     * @covers \Netgen\Layouts\Block\BlockDefinitionFactory::processConfig
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
            [],
        );
    }
}
