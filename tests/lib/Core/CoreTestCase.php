<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core;

use Netgen\BlockManager\API\Service\BlockService as APIBlockService;
use Netgen\BlockManager\API\Service\CollectionService as APICollectionService;
use Netgen\BlockManager\API\Service\LayoutResolverService as APILayoutResolverService;
use Netgen\BlockManager\API\Service\LayoutService as APILayoutService;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Block\ContainerDefinition;
use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry;
use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface;
use Netgen\BlockManager\Collection\Item\ItemDefinition;
use Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry;
use Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistryInterface;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistry;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface;
use Netgen\BlockManager\Config\ConfigDefinition;
use Netgen\BlockManager\Core\Mapper\BlockMapper;
use Netgen\BlockManager\Core\Mapper\CollectionMapper;
use Netgen\BlockManager\Core\Mapper\ConfigMapper;
use Netgen\BlockManager\Core\Mapper\LayoutMapper;
use Netgen\BlockManager\Core\Mapper\LayoutResolverMapper;
use Netgen\BlockManager\Core\Mapper\ParameterMapper;
use Netgen\BlockManager\Core\Service\BlockService;
use Netgen\BlockManager\Core\Service\CollectionService;
use Netgen\BlockManager\Core\Service\LayoutResolverService;
use Netgen\BlockManager\Core\Service\LayoutService;
use Netgen\BlockManager\Core\StructBuilder\BlockStructBuilder;
use Netgen\BlockManager\Core\StructBuilder\CollectionStructBuilder;
use Netgen\BlockManager\Core\StructBuilder\ConfigStructBuilder;
use Netgen\BlockManager\Core\StructBuilder\LayoutResolverStructBuilder;
use Netgen\BlockManager\Core\StructBuilder\LayoutStructBuilder;
use Netgen\BlockManager\Core\Validator\BlockValidator;
use Netgen\BlockManager\Core\Validator\CollectionValidator;
use Netgen\BlockManager\Core\Validator\LayoutResolverValidator;
use Netgen\BlockManager\Core\Validator\LayoutValidator;
use Netgen\BlockManager\Item\CmsItemLoaderInterface;
use Netgen\BlockManager\Item\Registry\ValueTypeRegistry;
use Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry;
use Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface;
use Netgen\BlockManager\Layout\Resolver\ConditionType;
use Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry;
use Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface;
use Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry;
use Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface;
use Netgen\BlockManager\Layout\Resolver\TargetType;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Layout\Type\Zone;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistryInterface;
use Netgen\BlockManager\Persistence\Handler\BlockHandlerInterface;
use Netgen\BlockManager\Persistence\Handler\CollectionHandlerInterface;
use Netgen\BlockManager\Persistence\Handler\LayoutHandlerInterface;
use Netgen\BlockManager\Persistence\Handler\LayoutResolverHandlerInterface;
use Netgen\BlockManager\Persistence\TransactionHandlerInterface;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandler;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandlerWithTranslatableParameter;
use Netgen\BlockManager\Tests\Block\Stubs\ContainerDefinitionHandler;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use Netgen\BlockManager\Tests\Config\Stubs\Block\ConfigHandler as BlockConfigHandler;
use Netgen\BlockManager\Tests\Config\Stubs\CollectionItem\ConfigHandler as ItemConfigHandler;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\ConditionType1;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\TargetType1;
use Netgen\BlockManager\Utils\HtmlPurifier;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class CoreTestCase extends TestCase
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
     * @var \Netgen\BlockManager\Persistence\TransactionHandlerInterface
     */
    protected $transactionHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\BlockHandlerInterface
     */
    protected $blockHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\LayoutHandlerInterface
     */
    protected $layoutHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\CollectionHandlerInterface
     */
    protected $collectionHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\LayoutResolverHandlerInterface
     */
    protected $layoutResolverHandler;

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
        $this->transactionHandler = $this->createTransactionHandler();
        $this->layoutHandler = $this->createLayoutHandler();
        $this->blockHandler = $this->createBlockHandler();
        $this->collectionHandler = $this->createCollectionHandler();
        $this->layoutResolverHandler = $this->createLayoutResolverHandler();

        $this->cmsItemLoaderMock = $this->createMock(CmsItemLoaderInterface::class);

        $this->parameterTypeRegistry = $this->parameterTypeRegistry ?? $this->createParameterTypeRegistry();
        $this->layoutTypeRegistry = $this->layoutTypeRegistry ?? $this->createLayoutTypeRegistry();
        $this->itemDefinitionRegistry = $this->itemDefinitionRegistry ?? $this->createItemDefinitionRegistry();
        $this->queryTypeRegistry = $this->queryTypeRegistry ?? $this->createQueryTypeRegistry();
        $this->blockDefinitionRegistry = $this->blockDefinitionRegistry ?? $this->createBlockDefinitionRegistry();
        $this->targetTypeRegistry = $this->targetTypeRegistry ?? $this->createTargetTypeRegistry();
        $this->conditionTypeRegistry = $this->conditionTypeRegistry ?? $this->createConditionTypeRegistry();

        $this->layoutService = $this->layoutService ?? $this->createLayoutService();
        $this->blockService = $this->blockService ?? $this->createBlockService();
        $this->collectionService = $this->collectionService ?? $this->createCollectionService();
        $this->layoutResolverService = $this->layoutResolverService ?? $this->createLayoutResolverService();
    }

    abstract protected function createTransactionHandler(): TransactionHandlerInterface;

    abstract protected function createLayoutHandler(): LayoutHandlerInterface;

    abstract protected function createBlockHandler(): BlockHandlerInterface;

    abstract protected function createCollectionHandler(): CollectionHandlerInterface;

    abstract protected function createLayoutResolverHandler(): LayoutResolverHandlerInterface;

    protected function createValidator(): ValidatorInterface
    {
        $validator = $this->createMock(ValidatorInterface::class);

        $validator->expects(self::any())
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        return $validator;
    }

    /**
     * Creates a layout service under test.
     */
    protected function createLayoutService(): APILayoutService
    {
        $layoutValidator = new LayoutValidator();
        $layoutValidator->setValidator($this->createValidator());

        return new LayoutService(
            $this->transactionHandler,
            $layoutValidator,
            $this->createLayoutMapper(),
            new LayoutStructBuilder(),
            $this->layoutHandler
        );
    }

    /**
     * Creates a block service under test.
     */
    protected function createBlockService(): APIBlockService
    {
        $validator = $this->createValidator();

        $collectionValidator = new CollectionValidator();
        $collectionValidator->setValidator($validator);

        $blockValidator = new BlockValidator($collectionValidator);
        $blockValidator->setValidator($validator);

        return new BlockService(
            $this->transactionHandler,
            $blockValidator,
            $this->createBlockMapper(),
            new BlockStructBuilder(
                new ConfigStructBuilder()
            ),
            $this->createParameterMapper(),
            $this->createConfigMapper(),
            $this->layoutService,
            $this->blockHandler,
            $this->layoutHandler,
            $this->collectionHandler
        );
    }

    /**
     * Creates a collection service under test.
     */
    protected function createCollectionService(): APICollectionService
    {
        $collectionValidator = new CollectionValidator();
        $collectionValidator->setValidator($this->createValidator());

        return new CollectionService(
            $this->transactionHandler,
            $collectionValidator,
            $this->createCollectionMapper(),
            new CollectionStructBuilder(
                new ConfigStructBuilder()
            ),
            $this->createParameterMapper(),
            $this->createConfigMapper(),
            $this->collectionHandler
        );
    }

    /**
     * Creates a layout resolver service under test.
     */
    protected function createLayoutResolverService(): APILayoutResolverService
    {
        $layoutResolverValidator = new LayoutResolverValidator(
            $this->targetTypeRegistry,
            $this->conditionTypeRegistry
        );

        $layoutResolverValidator->setValidator($this->createValidator());

        return new LayoutResolverService(
            $this->transactionHandler,
            $layoutResolverValidator,
            $this->createLayoutResolverMapper(),
            new LayoutResolverStructBuilder(),
            $this->layoutResolverHandler,
            $this->layoutHandler
        );
    }

    /**
     * Creates a layout mapper under test.
     */
    protected function createLayoutMapper(): LayoutMapper
    {
        return new LayoutMapper(
            $this->layoutHandler,
            $this->layoutTypeRegistry
        );
    }

    /**
     * Creates a block mapper under test.
     */
    protected function createBlockMapper(): BlockMapper
    {
        return new BlockMapper(
            $this->blockHandler,
            $this->collectionHandler,
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
            $this->collectionHandler,
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
            $this->layoutResolverHandler,
            $this->targetTypeRegistry,
            $this->conditionTypeRegistry,
            $this->layoutService
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

    protected function createLayoutTypeRegistry(): LayoutTypeRegistryInterface
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

        return new LayoutTypeRegistry(
            [
                '4_zones_a' => $layoutType1,
                '4_zones_b' => $layoutType2,
            ]
        );
    }

    protected function createItemDefinitionRegistry(): ItemDefinitionRegistryInterface
    {
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

        return new ItemDefinitionRegistry(['my_value_type' => $itemDefinition]);
    }

    protected function createQueryTypeRegistry(): QueryTypeRegistryInterface
    {
        return new QueryTypeRegistry(['my_query_type' => new QueryType('my_query_type')]);
    }

    protected function createBlockDefinitionRegistry(): BlockDefinitionRegistryInterface
    {
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

        return new BlockDefinitionRegistry(
            [
                'title' => $blockDefinition1,
                'text' => $blockDefinition2,
                'gallery' => $blockDefinition3,
                'list' => $blockDefinition4,
                'column' => $blockDefinition5,
                'two_columns' => $blockDefinition6,
            ]
        );
    }

    protected function createTargetTypeRegistry(): TargetTypeRegistryInterface
    {
        return new TargetTypeRegistry(
            [
                new TargetType1(),
                new TargetType\Route(),
                new TargetType\RoutePrefix(),
                new TargetType\PathInfo(),
                new TargetType\PathInfoPrefix(),
                new TargetType\RequestUri(),
                new TargetType\RequestUriPrefix(),
            ]
        );
    }

    protected function createConditionTypeRegistry(): ConditionTypeRegistryInterface
    {
        return new ConditionTypeRegistry(
            [
                new ConditionType1(),
                new ConditionType\RouteParameter(),
            ]
        );
    }

    protected function createParameterTypeRegistry(): ParameterTypeRegistryInterface
    {
        $remoteIdConverter = new RemoteIdConverter($this->cmsItemLoaderMock);

        return new ParameterTypeRegistry(
            [
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
                new ParameterType\Compound\BooleanType(),
            ]
        );
    }
}
