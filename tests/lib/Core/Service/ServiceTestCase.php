<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\API\Service\BlockService as APIBlockService;
use Netgen\BlockManager\API\Service\CollectionService as APICollectionService;
use Netgen\BlockManager\API\Service\LayoutResolverService as APILayoutResolverService;
use Netgen\BlockManager\API\Service\LayoutService as APILayoutService;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Block\ContainerDefinition;
use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry;
use Netgen\BlockManager\Collection\Item\ItemDefinition;
use Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistry;
use Netgen\BlockManager\Config\ConfigDefinition;
use Netgen\BlockManager\Core\Service\BlockService;
use Netgen\BlockManager\Core\Service\CollectionService;
use Netgen\BlockManager\Core\Service\LayoutResolverService;
use Netgen\BlockManager\Core\Service\LayoutService;
use Netgen\BlockManager\Core\Service\Mapper\BlockMapper;
use Netgen\BlockManager\Core\Service\Mapper\CollectionMapper;
use Netgen\BlockManager\Core\Service\Mapper\ConfigMapper;
use Netgen\BlockManager\Core\Service\Mapper\LayoutMapper;
use Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper;
use Netgen\BlockManager\Core\Service\Mapper\ParameterMapper;
use Netgen\BlockManager\Core\Service\StructBuilder\BlockStructBuilder;
use Netgen\BlockManager\Core\Service\StructBuilder\CollectionStructBuilder;
use Netgen\BlockManager\Core\Service\StructBuilder\ConfigStructBuilder;
use Netgen\BlockManager\Core\Service\StructBuilder\LayoutResolverStructBuilder;
use Netgen\BlockManager\Core\Service\StructBuilder\LayoutStructBuilder;
use Netgen\BlockManager\Core\Service\Validator\BlockValidator;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Item\CmsItemLoaderInterface;
use Netgen\BlockManager\Item\Registry\ValueTypeRegistry;
use Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry;
use Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry;
use Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Layout\Type\Zone;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandler;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandlerWithTranslatableParameter;
use Netgen\BlockManager\Tests\Block\Stubs\ContainerDefinitionHandler;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use Netgen\BlockManager\Tests\Config\Stubs\Block\ConfigHandler as BlockConfigHandler;
use Netgen\BlockManager\Tests\Config\Stubs\CollectionItem\ConfigHandler as ItemConfigHandler;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\ConditionType;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\TargetType;
use Netgen\BlockManager\Utils\HtmlPurifier;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class ServiceTestCase extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Item\CmsItemLoaderInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    protected $cmsItemLoaderMock;

    /**
     * @var \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface
     */
    protected $layoutTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistryInterface
     */
    protected $itemDefinitionRegistry;

    /**
     * @var \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface
     */
    protected $queryTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface
     */
    protected $blockDefinitionRegistry;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface
     */
    protected $targetTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface
     */
    protected $conditionTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistryInterface
     */
    protected $parameterTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Persistence\HandlerInterface
     */
    protected $persistenceHandler;

    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    protected $collectionService;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    protected $layoutResolverService;

    public function setUp(): void
    {
        $this->cmsItemLoaderMock = $this->createMock(CmsItemLoaderInterface::class);

        $this->prepareRegistries();
        $this->preparePersistence();
    }

    /**
     * Prepares the persistence handler used in tests.
     */
    abstract public function preparePersistence(): void;

    /**
     * Prepares the registries used in tests.
     */
    protected function prepareRegistries(): void
    {
        $layoutType1 = LayoutType::fromArray(
            [
                'identifier' => '4_zones_a',
                'zones' => [
                    'top' => new Zone(),
                    'left' => new Zone(),
                    'right' => Zone::fromArray(['allowedBlockDefinitions' => ['title', 'list']]),
                    'bottom' => Zone::fromArray(['allowedBlockDefinitions' => ['title']]),
                ],
            ]
        );

        $layoutType2 = LayoutType::fromArray(
            [
                'identifier' => '4_zones_b',
                'zones' => [
                    'top' => new Zone(),
                    'left' => new Zone(),
                    'right' => new Zone(),
                    'bottom' => new Zone(),
                ],
            ]
        );

        $this->layoutTypeRegistry = new LayoutTypeRegistry(
            [
                '4_zones_a' => $layoutType1,
                '4_zones_b' => $layoutType2,
            ]
        );

        $itemConfigHandler = new ItemConfigHandler();
        $itemConfigDefinition = ConfigDefinition::fromArray(
            [
                'parameterDefinitions' => $itemConfigHandler->getParameterDefinitions(),
            ]
        );

        $itemDefinition = ItemDefinition::fromArray(
            [
                'valueType' => 'my_value_type',
                'configDefinitions' => [
                    'key' => $itemConfigDefinition,
                ],
            ]
        );

        $this->itemDefinitionRegistry = new ItemDefinitionRegistry(['my_value_type' => $itemDefinition]);

        $this->queryTypeRegistry = new QueryTypeRegistry(['my_query_type' => new QueryType('my_query_type')]);

        $configHandler = new BlockConfigHandler();
        $configDefinition = ConfigDefinition::fromArray(
            [
                'parameterDefinitions' => $configHandler->getParameterDefinitions(),
            ]
        );

        $blockDefinitionHandler1 = new BlockDefinitionHandler();
        $blockDefinitionHandler2 = new BlockDefinitionHandlerWithTranslatableParameter();

        $blockDefinition1 = BlockDefinition::fromArray(
            [
                'identifier' => 'title',
                'parameterDefinitions' => $blockDefinitionHandler1->getParameterDefinitions(),
                'configDefinitions' => ['key' => $configDefinition],
                'isTranslatable' => true,
                'viewTypes' => [
                    'small' => ViewType::fromArray(
                        [
                            'itemViewTypes' => [
                                'standard' => new ItemViewType(),
                            ],
                        ]
                    ),
                ],
            ]
        );

        $blockDefinition2 = BlockDefinition::fromArray(
            [
                'identifier' => 'text',
                'parameterDefinitions' => $blockDefinitionHandler1->getParameterDefinitions(),
                'configDefinitions' => ['key' => $configDefinition],
                'isTranslatable' => false,
                'viewTypes' => [
                    'standard' => ViewType::fromArray(
                        [
                            'itemViewTypes' => [
                                'standard' => new ItemViewType(),
                            ],
                        ]
                    ),
                ],
            ]
        );

        $blockDefinition3 = BlockDefinition::fromArray(
            [
                'identifier' => 'gallery',
                'parameterDefinitions' => $blockDefinitionHandler2->getParameterDefinitions(),
                'configDefinitions' => ['key' => $configDefinition],
                'isTranslatable' => false,
                'collections' => ['default' => new Collection()],
                'viewTypes' => [
                    'standard' => ViewType::fromArray(
                        [
                            'itemViewTypes' => [
                                'standard' => new ItemViewType(),
                            ],
                        ]
                    ),
                ],
            ]
        );

        $blockDefinition4 = BlockDefinition::fromArray(
            [
                'identifier' => 'list',
                'parameterDefinitions' => $blockDefinitionHandler2->getParameterDefinitions(),
                'configDefinitions' => ['key' => $configDefinition],
                'isTranslatable' => false,
                'collections' => ['default' => new Collection()],
                'viewTypes' => [
                    'small' => ViewType::fromArray(
                        [
                            'itemViewTypes' => [
                                'standard' => new ItemViewType(),
                            ],
                        ]
                    ),
                ],
            ]
        );

        $blockDefinition5 = ContainerDefinition::fromArray(
            [
                'identifier' => 'column',
                'configDefinitions' => ['key' => $configDefinition],
                'handler' => new ContainerDefinitionHandler([], ['main', 'other']),
                'viewTypes' => [
                    'column' => ViewType::fromArray(
                        [
                            'itemViewTypes' => [
                                'standard' => new ItemViewType(),
                            ],
                        ]
                    ),
                ],
            ]
        );

        $blockDefinition6 = ContainerDefinition::fromArray(
            [
                'identifier' => 'two_columns',
                'configDefinitions' => ['key' => $configDefinition],
                'handler' => new ContainerDefinitionHandler([], ['left', 'right']),
                'viewTypes' => [
                    'two_columns_50_50' => ViewType::fromArray(
                        [
                            'itemViewTypes' => [
                                'standard' => new ItemViewType(),
                            ],
                        ]
                    ),
                ],
            ]
        );

        $this->blockDefinitionRegistry = new BlockDefinitionRegistry(
            [
                'title' => $blockDefinition1,
                'text' => $blockDefinition2,
                'gallery' => $blockDefinition3,
                'list' => $blockDefinition4,
                'column' => $blockDefinition5,
                'two_columns' => $blockDefinition6,
            ]
        );

        $this->targetTypeRegistry = new TargetTypeRegistry(
            new TargetType('target'),
            new TargetType('route'),
            new TargetType('route_prefix'),
            new TargetType('path_info'),
            new TargetType('path_info_prefix'),
            new TargetType('request_uri'),
            new TargetType('request_uri_prefix')
        );

        $this->conditionTypeRegistry = new ConditionTypeRegistry(
            new ConditionType('my_condition'),
            new ConditionType('route_parameter')
        );

        $this->prepareParameterTypeRegistry();
    }

    /**
     * Creates a layout service under test.
     */
    protected function createLayoutService(?LayoutValidator $layoutValidator = null): APILayoutService
    {
        if ($layoutValidator === null) {
            $validator = $this->createMock(ValidatorInterface::class);

            $validator->expects($this->any())
                ->method('validate')
                ->will($this->returnValue(new ConstraintViolationList()));

            $layoutValidator = new LayoutValidator();
            $layoutValidator->setValidator($validator);
        }

        return new LayoutService(
            $this->persistenceHandler,
            $layoutValidator,
            $this->createLayoutMapper(),
            new LayoutStructBuilder()
        );
    }

    /**
     * Creates a block service under test.
     */
    protected function createBlockService(?BlockValidator $blockValidator = null): APIBlockService
    {
        if ($blockValidator === null) {
            $validator = $this->createMock(ValidatorInterface::class);

            $validator->expects($this->any())
                ->method('validate')
                ->will($this->returnValue(new ConstraintViolationList()));

            $collectionValidator = new CollectionValidator();
            $collectionValidator->setValidator($validator);

            $blockValidator = new BlockValidator($collectionValidator);
            $blockValidator->setValidator($validator);
        }

        return new BlockService(
            $this->persistenceHandler,
            $blockValidator,
            $this->createBlockMapper(),
            new BlockStructBuilder(
                new ConfigStructBuilder()
            ),
            $this->createParameterMapper(),
            $this->createConfigMapper(),
            $this->createLayoutService()
        );
    }

    /**
     * Creates a collection service under test.
     */
    protected function createCollectionService(?CollectionValidator $collectionValidator = null): APICollectionService
    {
        if ($collectionValidator === null) {
            $validator = $this->createMock(ValidatorInterface::class);

            $validator->expects($this->any())
                ->method('validate')
                ->will($this->returnValue(new ConstraintViolationList()));

            $collectionValidator = new CollectionValidator();
            $collectionValidator->setValidator($validator);
        }

        return new CollectionService(
            $this->persistenceHandler,
            $collectionValidator,
            $this->createCollectionMapper(),
            new CollectionStructBuilder(
                new ConfigStructBuilder()
            ),
            $this->createParameterMapper(),
            $this->createConfigMapper()
        );
    }

    /**
     * Creates a layout resolver service under test.
     */
    protected function createLayoutResolverService(?LayoutResolverValidator $layoutResolverValidator = null): APILayoutResolverService
    {
        if ($layoutResolverValidator === null) {
            $validator = $this->createMock(ValidatorInterface::class);

            $validator->expects($this->any())
                ->method('validate')
                ->will($this->returnValue(new ConstraintViolationList()));

            $layoutResolverValidator = new LayoutResolverValidator(
                $this->targetTypeRegistry,
                $this->conditionTypeRegistry
            );

            $layoutResolverValidator->setValidator($validator);
        }

        return new LayoutResolverService(
            $this->persistenceHandler,
            $layoutResolverValidator,
            $this->createLayoutResolverMapper(),
            new LayoutResolverStructBuilder()
        );
    }

    /**
     * Creates a layout mapper under test.
     */
    protected function createLayoutMapper(): LayoutMapper
    {
        return new LayoutMapper(
            $this->persistenceHandler->getLayoutHandler(),
            $this->layoutTypeRegistry
        );
    }

    /**
     * Creates a block mapper under test.
     */
    protected function createBlockMapper(): BlockMapper
    {
        return new BlockMapper(
            $this->persistenceHandler->getBlockHandler(),
            $this->persistenceHandler->getCollectionHandler(),
            $this->createCollectionMapper(),
            $this->createParameterMapper(),
            $this->createConfigMapper(),
            $this->blockDefinitionRegistry
        );
    }

    /**
     * Creates a collection mapper under test.
     */
    protected function createCollectionMapper(): CollectionMapper
    {
        return new CollectionMapper(
            $this->persistenceHandler->getCollectionHandler(),
            $this->createParameterMapper(),
            $this->createConfigMapper(),
            $this->itemDefinitionRegistry,
            $this->queryTypeRegistry,
            $this->cmsItemLoaderMock
        );
    }

    /**
     * Creates a layout resolver mapper under test.
     */
    protected function createLayoutResolverMapper(): LayoutResolverMapper
    {
        return new LayoutResolverMapper(
            $this->persistenceHandler->getLayoutResolverHandler(),
            $this->targetTypeRegistry,
            $this->conditionTypeRegistry,
            $this->createLayoutService()
        );
    }

    /**
     * Creates the parameter mapper under test.
     */
    protected function createParameterMapper(): ParameterMapper
    {
        return new ParameterMapper();
    }

    /**
     * Creates the config mapper under test.
     */
    protected function createConfigMapper(): ConfigMapper
    {
        return new ConfigMapper($this->createParameterMapper());
    }

    protected function prepareParameterTypeRegistry(): void
    {
        $remoteIdConverter = new RemoteIdConverter($this->cmsItemLoaderMock);

        $this->parameterTypeRegistry = new ParameterTypeRegistry(
            new ParameterType\TextLineType(),
            new ParameterType\TextType(),
            new ParameterType\UrlType(),
            new ParameterType\RangeType(),
            new ParameterType\NumberType(),
            new ParameterType\LinkType(new ValueTypeRegistry([]), $remoteIdConverter),
            new ParameterType\ItemLinkType(new ValueTypeRegistry([]), $remoteIdConverter),
            new ParameterType\IntegerType(),
            new ParameterType\IdentifierType(),
            new ParameterType\HtmlType(new HtmlPurifier()),
            new ParameterType\EmailType(),
            new ParameterType\ChoiceType(),
            new ParameterType\BooleanType(),
            new ParameterType\DateTimeType(),
            new ParameterType\Compound\BooleanType()
        );
    }
}
