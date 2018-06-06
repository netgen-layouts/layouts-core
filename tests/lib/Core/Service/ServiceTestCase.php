<?php

namespace Netgen\BlockManager\Tests\Core\Service;

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
use Netgen\BlockManager\Item\ItemLoaderInterface;
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
use Netgen\BlockManager\Tests\Config\Stubs\Block\HttpCacheConfigHandler;
use Netgen\BlockManager\Tests\Config\Stubs\CollectionItem\VisibilityConfigHandler;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\ConditionType;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\TargetType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class ServiceTestCase extends TestCase
{
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

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\BlockMapper
     */
    protected $blockMapper;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper
     */
    protected $collectionMapper;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper
     */
    protected $layoutMapper;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper
     */
    protected $layoutResolverMapper;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $itemLoaderMock;

    public function setUp()
    {
        $this->itemLoaderMock = $this->createMock(ItemLoaderInterface::class);

        $this->prepareRegistries();
        $this->preparePersistence();
    }

    /**
     * Prepares the persistence handler used in tests.
     */
    abstract public function preparePersistence();

    /**
     * Prepares the registries used in tests.
     */
    protected function prepareRegistries()
    {
        $layoutType1 = new LayoutType(
            [
                'identifier' => '4_zones_a',
                'zones' => [
                    'top' => new Zone(),
                    'left' => new Zone(),
                    'right' => new Zone(['allowedBlockDefinitions' => ['title', 'list']]),
                    'bottom' => new Zone(['allowedBlockDefinitions' => ['title']]),
                ],
            ]
        );

        $layoutType2 = new LayoutType(
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

        $this->layoutTypeRegistry = new LayoutTypeRegistry();
        $this->layoutTypeRegistry->addLayoutType('4_zones_a', $layoutType1);
        $this->layoutTypeRegistry->addLayoutType('4_zones_b', $layoutType2);

        $itemVisibilityHandler = new VisibilityConfigHandler();
        $itemVisibilityDefinition = new ConfigDefinition(
            [
                'parameterDefinitions' => $itemVisibilityHandler->getParameterDefinitions(),
            ]
        );

        $itemDefinition = new ItemDefinition(
            [
                'valueType' => 'my_value_type',
                'configDefinitions' => [
                    'visibility' => $itemVisibilityDefinition,
                ],
            ]
        );

        $this->itemDefinitionRegistry = new ItemDefinitionRegistry();
        $this->itemDefinitionRegistry->addItemDefinition('my_value_type', $itemDefinition);

        $this->queryTypeRegistry = new QueryTypeRegistry();
        $this->queryTypeRegistry->addQueryType('my_query_type', new QueryType('my_query_type'));

        $httpCacheHandler = new HttpCacheConfigHandler();
        $httpCacheDefinition = new ConfigDefinition(
            [
                'parameterDefinitions' => $httpCacheHandler->getParameterDefinitions(),
            ]
        );

        $blockDefinitionHandler1 = new BlockDefinitionHandler();
        $blockDefinitionHandler2 = new BlockDefinitionHandlerWithTranslatableParameter();

        $blockDefinition1 = new BlockDefinition(
            [
                'identifier' => 'title',
                'parameterDefinitions' => $blockDefinitionHandler1->getParameterDefinitions(),
                'configDefinitions' => ['http_cache' => $httpCacheDefinition],
                'isTranslatable' => true,
                'viewTypes' => [
                    'small' => new ViewType(
                        [
                            'itemViewTypes' => [
                                'standard' => new ItemViewType(),
                            ],
                        ]
                    ),
                ],
            ]
        );

        $blockDefinition2 = new BlockDefinition(
            [
                'identifier' => 'text',
                'parameterDefinitions' => $blockDefinitionHandler1->getParameterDefinitions(),
                'configDefinitions' => ['http_cache' => $httpCacheDefinition],
                'isTranslatable' => false,
                'viewTypes' => [
                    'standard' => new ViewType(
                        [
                            'itemViewTypes' => [
                                'standard' => new ItemViewType(),
                            ],
                        ]
                    ),
                ],
            ]
        );

        $blockDefinition3 = new BlockDefinition(
            [
                'identifier' => 'gallery',
                'parameterDefinitions' => $blockDefinitionHandler2->getParameterDefinitions(),
                'configDefinitions' => ['http_cache' => $httpCacheDefinition],
                'isTranslatable' => false,
                'collections' => ['default' => new Collection()],
                'viewTypes' => [
                    'standard' => new ViewType(
                        [
                            'itemViewTypes' => [
                                'standard' => new ItemViewType(),
                            ],
                        ]
                    ),
                ],
            ]
        );

        $blockDefinition4 = new BlockDefinition(
            [
                'identifier' => 'list',
                'parameterDefinitions' => $blockDefinitionHandler2->getParameterDefinitions(),
                'configDefinitions' => ['http_cache' => $httpCacheDefinition],
                'isTranslatable' => false,
                'collections' => ['default' => new Collection()],
                'viewTypes' => [
                    'small' => new ViewType(
                        [
                            'itemViewTypes' => [
                                'standard' => new ItemViewType(),
                            ],
                        ]
                    ),
                ],
            ]
        );

        $blockDefinition5 = new ContainerDefinition(
            [
                'identifier' => 'column',
                'configDefinitions' => ['http_cache' => $httpCacheDefinition],
                'handler' => new ContainerDefinitionHandler([], ['main', 'other']),
                'viewTypes' => [
                    'column' => new ViewType(
                        [
                            'itemViewTypes' => [
                                'standard' => new ItemViewType(),
                            ],
                        ]
                    ),
                ],
            ]
        );

        $blockDefinition6 = new ContainerDefinition(
            [
                'identifier' => 'two_columns',
                'configDefinitions' => ['http_cache' => $httpCacheDefinition],
                'handler' => new ContainerDefinitionHandler([], ['left', 'right']),
                'viewTypes' => [
                    'two_columns_50_50' => new ViewType(
                        [
                            'itemViewTypes' => [
                                'standard' => new ItemViewType(),
                            ],
                        ]
                    ),
                ],
            ]
        );

        $this->blockDefinitionRegistry = new BlockDefinitionRegistry();
        $this->blockDefinitionRegistry->addBlockDefinition('title', $blockDefinition1);
        $this->blockDefinitionRegistry->addBlockDefinition('text', $blockDefinition2);
        $this->blockDefinitionRegistry->addBlockDefinition('gallery', $blockDefinition3);
        $this->blockDefinitionRegistry->addBlockDefinition('list', $blockDefinition4);
        $this->blockDefinitionRegistry->addBlockDefinition('column', $blockDefinition5);
        $this->blockDefinitionRegistry->addBlockDefinition('two_columns', $blockDefinition6);

        $this->targetTypeRegistry = new TargetTypeRegistry();
        $this->targetTypeRegistry->addTargetType(new TargetType('target'));
        $this->targetTypeRegistry->addTargetType(new TargetType('route'));
        $this->targetTypeRegistry->addTargetType(new TargetType('route_prefix'));
        $this->targetTypeRegistry->addTargetType(new TargetType('path_info'));
        $this->targetTypeRegistry->addTargetType(new TargetType('path_info_prefix'));
        $this->targetTypeRegistry->addTargetType(new TargetType('request_uri'));
        $this->targetTypeRegistry->addTargetType(new TargetType('request_uri_prefix'));

        $this->conditionTypeRegistry = new ConditionTypeRegistry();
        $this->conditionTypeRegistry->addConditionType(new ConditionType('my_condition'));
        $this->conditionTypeRegistry->addConditionType(new ConditionType('route_parameter'));

        $this->prepareParameterTypeRegistry();
    }

    /**
     * Creates a layout service under test.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\LayoutValidator $layoutValidator
     *
     * @return \Netgen\BlockManager\Core\Service\LayoutService
     */
    protected function createLayoutService(LayoutValidator $layoutValidator = null)
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
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\BlockValidator $blockValidator
     *
     * @return \Netgen\BlockManager\API\Service\BlockService
     */
    protected function createBlockService(BlockValidator $blockValidator = null)
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
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\CollectionValidator $collectionValidator
     *
     * @return \Netgen\BlockManager\API\Service\CollectionService
     */
    protected function createCollectionService(CollectionValidator $collectionValidator = null)
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
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator $layoutResolverValidator
     *
     * @return \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    protected function createLayoutResolverService(LayoutResolverValidator $layoutResolverValidator = null)
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
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper
     */
    protected function createLayoutMapper()
    {
        return new LayoutMapper(
            $this->persistenceHandler->getLayoutHandler(),
            $this->layoutTypeRegistry
        );
    }

    /**
     * Creates a block mapper under test.
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper\BlockMapper
     */
    protected function createBlockMapper()
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
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper
     */
    protected function createCollectionMapper()
    {
        return new CollectionMapper(
            $this->persistenceHandler->getCollectionHandler(),
            $this->createParameterMapper(),
            $this->createConfigMapper(),
            $this->itemDefinitionRegistry,
            $this->queryTypeRegistry,
            $this->itemLoaderMock
        );
    }

    /**
     * Creates a layout resolver mapper under test.
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper
     */
    protected function createLayoutResolverMapper()
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
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper
     */
    protected function createParameterMapper()
    {
        return new ParameterMapper();
    }

    /**
     * Creates the config mapper under test.
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper\ConfigMapper
     */
    protected function createConfigMapper()
    {
        return new ConfigMapper($this->createParameterMapper());
    }

    protected function prepareParameterTypeRegistry()
    {
        $remoteIdConverter = new RemoteIdConverter($this->itemLoaderMock);

        $this->parameterTypeRegistry = new ParameterTypeRegistry();
        $this->parameterTypeRegistry->addParameterType(new ParameterType\TextLineType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\TextType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\UrlType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\RangeType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\NumberType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\LinkType(new ValueTypeRegistry(), $remoteIdConverter));
        $this->parameterTypeRegistry->addParameterType(new ParameterType\ItemLinkType(new ValueTypeRegistry(), $remoteIdConverter));
        $this->parameterTypeRegistry->addParameterType(new ParameterType\IntegerType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\IdentifierType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\HtmlType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\EmailType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\ChoiceType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\BooleanType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\DateTimeType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\Compound\BooleanType());
    }
}
