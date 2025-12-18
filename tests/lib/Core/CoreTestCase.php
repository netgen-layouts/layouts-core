<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core;

use Netgen\Layouts\API\Service\BlockService as APIBlockService;
use Netgen\Layouts\API\Service\CollectionService as APICollectionService;
use Netgen\Layouts\API\Service\LayoutResolverService as APILayoutResolverService;
use Netgen\Layouts\API\Service\LayoutService as APILayoutService;
use Netgen\Layouts\API\Service\TransactionService as APITransactionService;
use Netgen\Layouts\Block\BlockDefinition\Handler\PagedCollectionsPlugin;
use Netgen\Layouts\Block\BlockDefinitionFactory;
use Netgen\Layouts\Block\Registry\BlockDefinitionRegistry;
use Netgen\Layouts\Block\Registry\HandlerPluginRegistry;
use Netgen\Layouts\Collection\Item\ItemDefinition;
use Netgen\Layouts\Collection\Registry\ItemDefinitionRegistry;
use Netgen\Layouts\Collection\Registry\QueryTypeRegistry;
use Netgen\Layouts\Config\ConfigDefinition;
use Netgen\Layouts\Config\ConfigDefinitionFactory;
use Netgen\Layouts\Core\Mapper\BlockMapper;
use Netgen\Layouts\Core\Mapper\CollectionMapper;
use Netgen\Layouts\Core\Mapper\ConfigMapper;
use Netgen\Layouts\Core\Mapper\LayoutMapper;
use Netgen\Layouts\Core\Mapper\LayoutResolverMapper;
use Netgen\Layouts\Core\Mapper\ParameterMapper;
use Netgen\Layouts\Core\Service\BlockService;
use Netgen\Layouts\Core\Service\CollectionService;
use Netgen\Layouts\Core\Service\LayoutResolverService;
use Netgen\Layouts\Core\Service\LayoutService;
use Netgen\Layouts\Core\Service\TransactionService;
use Netgen\Layouts\Core\StructBuilder\BlockStructBuilder;
use Netgen\Layouts\Core\StructBuilder\CollectionStructBuilder;
use Netgen\Layouts\Core\StructBuilder\ConfigStructBuilder;
use Netgen\Layouts\Core\StructBuilder\LayoutResolverStructBuilder;
use Netgen\Layouts\Core\StructBuilder\LayoutStructBuilder;
use Netgen\Layouts\Core\Validator\BlockValidator;
use Netgen\Layouts\Core\Validator\CollectionValidator;
use Netgen\Layouts\Core\Validator\LayoutResolverValidator;
use Netgen\Layouts\Core\Validator\LayoutValidator;
use Netgen\Layouts\Item\CmsItemLoaderInterface;
use Netgen\Layouts\Item\Registry\ValueTypeRegistry;
use Netgen\Layouts\Layout\Registry\LayoutTypeRegistry;
use Netgen\Layouts\Layout\Resolver\ConditionType;
use Netgen\Layouts\Layout\Resolver\Registry\ConditionTypeRegistry;
use Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry;
use Netgen\Layouts\Layout\Resolver\TargetType;
use Netgen\Layouts\Layout\Type\LayoutType;
use Netgen\Layouts\Layout\Type\Zone;
use Netgen\Layouts\Parameters\ParameterBuilderFactory;
use Netgen\Layouts\Parameters\ParameterType;
use Netgen\Layouts\Parameters\ParameterType\ItemLink\RemoteIdConverter;
use Netgen\Layouts\Parameters\Registry\ParameterTypeRegistry;
use Netgen\Layouts\Persistence\Handler\BlockHandlerInterface;
use Netgen\Layouts\Persistence\Handler\CollectionHandlerInterface;
use Netgen\Layouts\Persistence\Handler\LayoutHandlerInterface;
use Netgen\Layouts\Persistence\Handler\LayoutResolverHandlerInterface;
use Netgen\Layouts\Persistence\TransactionHandlerInterface;
use Netgen\Layouts\Standard\Block\BlockDefinition\Handler\Container\ColumnHandler;
use Netgen\Layouts\Standard\Block\BlockDefinition\Handler\Container\TwoColumnsHandler;
use Netgen\Layouts\Standard\Block\BlockDefinition\Handler\ListHandler;
use Netgen\Layouts\Standard\Block\BlockDefinition\Handler\TextHandler;
use Netgen\Layouts\Standard\Block\BlockDefinition\Handler\TitleHandler;
use Netgen\Layouts\Tests\Block\Stubs\BlockDefinitionHandlerWithTranslatableParameter;
use Netgen\Layouts\Tests\Collection\Stubs\QueryType;
use Netgen\Layouts\Tests\Config\Stubs\Block\ConfigHandler as BlockConfigHandler;
use Netgen\Layouts\Tests\Config\Stubs\CollectionItem\ConfigHandler as ItemConfigHandler;
use Netgen\Layouts\Tests\Core\Stubs\ConfigProvider;
use Netgen\Layouts\Tests\Layout\Resolver\Stubs\ConditionType1;
use Netgen\Layouts\Tests\Layout\Resolver\Stubs\TargetType1;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCaseTrait;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;
use Symfony\Component\Uid\Factory\UuidFactory;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class CoreTestCase extends TestCase
{
    use ValidatorTestCaseTrait;

    final protected ValidatorInterface $validator;

    final protected UuidFactory $uuidFactory;

    final protected Stub&CmsItemLoaderInterface $cmsItemLoaderStub;

    final protected TransactionHandlerInterface $transactionHandler;

    final protected CollectionHandlerInterface $collectionHandler;

    final protected BlockHandlerInterface $blockHandler;

    final protected LayoutHandlerInterface $layoutHandler;

    final protected LayoutResolverHandlerInterface $layoutResolverHandler;

    final protected ParameterTypeRegistry $parameterTypeRegistry;

    final protected LayoutTypeRegistry $layoutTypeRegistry;

    final protected ItemDefinitionRegistry $itemDefinitionRegistry;

    final protected QueryTypeRegistry $queryTypeRegistry;

    final protected BlockDefinitionRegistry $blockDefinitionRegistry;

    final protected TargetTypeRegistry $targetTypeRegistry;

    final protected ConditionTypeRegistry $conditionTypeRegistry;

    final protected APITransactionService $transactionService;

    final protected ParameterMapper $parameterMapper;

    final protected ConfigMapper $configMapper;

    final protected LayoutMapper $layoutMapper;

    final protected APILayoutService $layoutService;

    final protected CollectionMapper $collectionMapper;

    final protected APICollectionService $collectionService;

    final protected BlockMapper $blockMapper;

    final protected APIBlockService $blockService;

    final protected LayoutResolverMapper $layoutResolverMapper;

    final protected APILayoutResolverService $layoutResolverService;

    protected function setUp(): void
    {
        $this->validator = $this->createValidator();

        $this->uuidFactory = $this->createUuidFactory();
        $this->cmsItemLoaderStub = self::createStub(CmsItemLoaderInterface::class);

        $this->transactionHandler = $this->createTransactionHandler();
        $this->collectionHandler = $this->createCollectionHandler();
        $this->blockHandler = $this->createBlockHandler();
        $this->layoutHandler = $this->createLayoutHandler();
        $this->layoutResolverHandler = $this->createLayoutResolverHandler();

        $this->parameterTypeRegistry = $this->createParameterTypeRegistry();
        $this->layoutTypeRegistry = $this->createLayoutTypeRegistry();
        $this->itemDefinitionRegistry = $this->createItemDefinitionRegistry();
        $this->queryTypeRegistry = $this->createQueryTypeRegistry();
        $this->blockDefinitionRegistry = $this->createBlockDefinitionRegistry();
        $this->targetTypeRegistry = $this->createTargetTypeRegistry();
        $this->conditionTypeRegistry = $this->createConditionTypeRegistry();

        $this->transactionService = $this->createTransactionService();

        $this->parameterMapper = $this->createParameterMapper();
        $this->configMapper = $this->createConfigMapper();

        $this->layoutMapper = $this->createLayoutMapper();
        $this->layoutService = $this->createLayoutService();

        $this->collectionMapper = $this->createCollectionMapper();
        $this->collectionService = $this->createCollectionService();

        $this->blockMapper = $this->createBlockMapper();
        $this->blockService = $this->createBlockService();

        $this->layoutResolverMapper = $this->createLayoutResolverMapper();
        $this->layoutResolverService = $this->createLayoutResolverService();
    }

    final protected function createUuidFactory(): UuidFactory
    {
        return new UuidFactory();
    }

    /**
     * Returns the persistence handler under test.
     */
    abstract protected function createTransactionHandler(): TransactionHandlerInterface;

    /**
     * Returns the collection handler under test.
     */
    abstract protected function createCollectionHandler(): CollectionHandlerInterface;

    /**
     * Returns the block handler under test.
     */
    abstract protected function createBlockHandler(): BlockHandlerInterface;

    /**
     * Returns the layout handler under test.
     */
    abstract protected function createLayoutHandler(): LayoutHandlerInterface;

    /**
     * Returns the layout resolver handler under test.
     */
    abstract protected function createLayoutResolverHandler(): LayoutResolverHandlerInterface;

    private function createParameterTypeRegistry(): ParameterTypeRegistry
    {
        $remoteIdConverter = new RemoteIdConverter($this->cmsItemLoaderStub);

        return new ParameterTypeRegistry(
            [
                ParameterType\TextLineType::getIdentifier() => new ParameterType\TextLineType(),
                ParameterType\TextType::getIdentifier() => new ParameterType\TextType(),
                ParameterType\UrlType::getIdentifier() => new ParameterType\UrlType(),
                ParameterType\RangeType::getIdentifier() => new ParameterType\RangeType(),
                ParameterType\NumberType::getIdentifier() => new ParameterType\NumberType(),
                ParameterType\LinkType::getIdentifier() => new ParameterType\LinkType(new ValueTypeRegistry([]), $remoteIdConverter),
                ParameterType\ItemLinkType::getIdentifier() => new ParameterType\ItemLinkType(new ValueTypeRegistry([]), $remoteIdConverter),
                ParameterType\IntegerType::getIdentifier() => new ParameterType\IntegerType(),
                ParameterType\IdentifierType::getIdentifier() => new ParameterType\IdentifierType(),
                ParameterType\HtmlType::getIdentifier() => new ParameterType\HtmlType(new HtmlSanitizer(new HtmlSanitizerConfig()->allowSafeElements())),
                ParameterType\EmailType::getIdentifier() => new ParameterType\EmailType(),
                ParameterType\ChoiceType::getIdentifier() => new ParameterType\ChoiceType(),
                ParameterType\EnumType::getIdentifier() => new ParameterType\EnumType(),
                ParameterType\BooleanType::getIdentifier() => new ParameterType\BooleanType(),
                ParameterType\DateTimeType::getIdentifier() => new ParameterType\DateTimeType(),
                ParameterType\HiddenType::getIdentifier() => new ParameterType\HiddenType(),
                ParameterType\Compound\BooleanType::getIdentifier() => new ParameterType\Compound\BooleanType(),
            ],
        );
    }

    private function createLayoutTypeRegistry(): LayoutTypeRegistry
    {
        $layoutType1 = LayoutType::fromArray(
            [
                'identifier' => 'test_layout_1',
                'zones' => [
                    'top' => new Zone(),
                    'left' => new Zone(),
                    'right' => Zone::fromArray(['allowedBlockDefinitions' => ['title', 'list']]),
                    'bottom' => Zone::fromArray(['allowedBlockDefinitions' => ['title']]),
                ],
            ],
        );

        $layoutType2 = LayoutType::fromArray(
            [
                'identifier' => 'test_layout_2',
                'zones' => [
                    'top' => new Zone(),
                    'left' => new Zone(),
                    'right' => new Zone(),
                    'bottom' => new Zone(),
                ],
            ],
        );

        return new LayoutTypeRegistry(
            [
                'test_layout_1' => $layoutType1,
                'test_layout_2' => $layoutType2,
            ],
        );
    }

    private function createItemDefinitionRegistry(): ItemDefinitionRegistry
    {
        $itemConfigHandler = new ItemConfigHandler();
        $itemConfigDefinition = ConfigDefinition::fromArray(
            [
                'parameterDefinitions' => $itemConfigHandler->getParameterDefinitions(),
            ],
        );

        $itemDefinition = ItemDefinition::fromArray(
            [
                'valueType' => 'test_value_type',
                'configDefinitions' => [
                    'key' => $itemConfigDefinition,
                ],
            ],
        );

        return new ItemDefinitionRegistry(['test_value_type' => $itemDefinition]);
    }

    private function createQueryTypeRegistry(): QueryTypeRegistry
    {
        return new QueryTypeRegistry(['test_query_type' => new QueryType('test_query_type')]);
    }

    private function createBlockDefinitionRegistry(): BlockDefinitionRegistry
    {
        $configDefinitionHandlers = ['key' => new BlockConfigHandler()];

        $handlerPluginRegistry = new HandlerPluginRegistry(
            [
                new PagedCollectionsPlugin(['pager' => 'pager', 'load_more' => 'load_more'], []),
            ],
        );

        $parameterBuilderFactory = new ParameterBuilderFactory(
            $this->parameterTypeRegistry,
        );

        $blockDefinitionFactory = new BlockDefinitionFactory(
            $parameterBuilderFactory,
            $handlerPluginRegistry,
            new ConfigDefinitionFactory($parameterBuilderFactory),
        );

        $blockDefinition1 = $blockDefinitionFactory->buildBlockDefinition(
            'title',
            new TitleHandler(['h1' => 'h1', 'h2' => 'h2', 'h3' => 'h3'], []),
            $configDefinitionHandlers,
            ConfigProvider::fromShortConfig(['standard' => ['standard']]),
            ['translatable' => false],
        );

        $blockDefinition2 = $blockDefinitionFactory->buildBlockDefinition(
            'text',
            new TextHandler(),
            $configDefinitionHandlers,
            ConfigProvider::fromShortConfig(['standard' => ['standard']]),
            ['translatable' => false],
        );

        $blockDefinition3 = $blockDefinitionFactory->buildBlockDefinition(
            'list',
            new ListHandler([2 => '2', 3 => '3', 4 => '4', 6 => '6']),
            $configDefinitionHandlers,
            ConfigProvider::fromShortConfig(['list' => ['standard'], 'grid' => ['standard_with_intro']]),
            ['translatable' => false],
        );

        $blockDefinition4 = $blockDefinitionFactory->buildContainerDefinition(
            'column',
            new ColumnHandler(),
            $configDefinitionHandlers,
            ConfigProvider::fromShortConfig(['column' => ['standard']]),
            ['translatable' => false],
        );

        $blockDefinition5 = $blockDefinitionFactory->buildContainerDefinition(
            'two_columns',
            new TwoColumnsHandler(),
            $configDefinitionHandlers,
            ConfigProvider::fromShortConfig(['two_columns_50_50' => ['standard']]),
            ['translatable' => false],
        );

        $blockDefinition6 = $blockDefinitionFactory->buildBlockDefinition(
            'translatable',
            new BlockDefinitionHandlerWithTranslatableParameter(),
            $configDefinitionHandlers,
            ConfigProvider::fromShortConfig(['small' => ['standard']]),
            ['translatable' => true],
        );

        return new BlockDefinitionRegistry(
            [
                'title' => $blockDefinition1,
                'text' => $blockDefinition2,
                'list' => $blockDefinition3,
                'column' => $blockDefinition4,
                'two_columns' => $blockDefinition5,
                'translatable' => $blockDefinition6,
            ],
        );
    }

    private function createTargetTypeRegistry(): TargetTypeRegistry
    {
        return new TargetTypeRegistry(
            [
                TargetType1::getType() => new TargetType1(),
                TargetType\Route::getType() => new TargetType\Route(),
                TargetType\RoutePrefix::getType() => new TargetType\RoutePrefix(),
                TargetType\PathInfo::getType() => new TargetType\PathInfo(),
                TargetType\PathInfoPrefix::getType() => new TargetType\PathInfoPrefix(),
                TargetType\RequestUri::getType() => new TargetType\RequestUri(),
                TargetType\RequestUriPrefix::getType() => new TargetType\RequestUriPrefix(),
            ],
        );
    }

    private function createConditionTypeRegistry(): ConditionTypeRegistry
    {
        return new ConditionTypeRegistry(
            [
                ConditionType1::getType() => new ConditionType1(),
                ConditionType\RouteParameter::getType() => new ConditionType\RouteParameter(),
            ],
        );
    }

    /**
     * Creates the parameter mapper under test.
     */
    private function createParameterMapper(): ParameterMapper
    {
        return new ParameterMapper();
    }

    /**
     * Creates the config mapper under test.
     */
    private function createConfigMapper(): ConfigMapper
    {
        return new ConfigMapper($this->parameterMapper);
    }

    /**
     * Creates a transaction service under test.
     */
    private function createTransactionService(): APITransactionService
    {
        return new TransactionService(
            $this->transactionHandler,
        );
    }

    /**
     * Creates a layout mapper under test.
     */
    private function createLayoutMapper(): LayoutMapper
    {
        return new LayoutMapper(
            $this->layoutHandler,
            $this->layoutTypeRegistry,
        );
    }

    /**
     * Creates a layout service under test.
     */
    private function createLayoutService(): APILayoutService
    {
        $layoutValidator = new LayoutValidator();
        $layoutValidator->setValidator($this->validator);

        return new LayoutService(
            $this->transactionHandler,
            $layoutValidator,
            $this->layoutMapper,
            new LayoutStructBuilder(),
            $this->layoutHandler,
        );
    }

    /**
     * Creates a collection mapper under test.
     */
    private function createCollectionMapper(): CollectionMapper
    {
        return new CollectionMapper(
            $this->collectionHandler,
            $this->parameterMapper,
            $this->configMapper,
            $this->itemDefinitionRegistry,
            $this->queryTypeRegistry,
            $this->cmsItemLoaderStub,
        );
    }

    /**
     * Creates a collection service under test.
     */
    private function createCollectionService(): APICollectionService
    {
        $collectionValidator = new CollectionValidator();
        $collectionValidator->setValidator($this->validator);

        return new CollectionService(
            $this->transactionHandler,
            $collectionValidator,
            $this->collectionMapper,
            new CollectionStructBuilder(
                new ConfigStructBuilder(),
            ),
            $this->parameterMapper,
            $this->configMapper,
            $this->collectionHandler,
        );
    }

    /**
     * Creates a block mapper under test.
     */
    private function createBlockMapper(): BlockMapper
    {
        return new BlockMapper(
            $this->blockHandler,
            $this->collectionHandler,
            $this->collectionMapper,
            $this->parameterMapper,
            $this->configMapper,
            $this->blockDefinitionRegistry,
        );
    }

    /**
     * Creates a block service under test.
     */
    private function createBlockService(): APIBlockService
    {
        $collectionValidator = new CollectionValidator();
        $collectionValidator->setValidator($this->validator);

        $blockValidator = new BlockValidator($collectionValidator);
        $blockValidator->setValidator($this->validator);

        return new BlockService(
            $this->transactionHandler,
            $blockValidator,
            $this->blockMapper,
            new BlockStructBuilder(
                new ConfigStructBuilder(),
            ),
            $this->parameterMapper,
            $this->configMapper,
            $this->layoutTypeRegistry,
            $this->blockHandler,
            $this->layoutHandler,
            $this->collectionHandler,
        );
    }

    /**
     * Creates a layout resolver mapper under test.
     */
    private function createLayoutResolverMapper(): LayoutResolverMapper
    {
        return new LayoutResolverMapper(
            $this->layoutResolverHandler,
            $this->targetTypeRegistry,
            $this->conditionTypeRegistry,
            $this->layoutService,
        );
    }

    /**
     * Creates a layout resolver service under test.
     */
    private function createLayoutResolverService(): APILayoutResolverService
    {
        $layoutResolverValidator = new LayoutResolverValidator(
            $this->targetTypeRegistry,
            $this->conditionTypeRegistry,
        );

        $layoutResolverValidator->setValidator($this->validator);

        return new LayoutResolverService(
            $this->transactionHandler,
            $layoutResolverValidator,
            $this->layoutResolverMapper,
            new LayoutResolverStructBuilder(),
            $this->layoutResolverHandler,
            $this->layoutHandler,
        );
    }
}
