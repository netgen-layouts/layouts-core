<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry;
use Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistry;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistry;
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
use Netgen\BlockManager\Core\Service\Validator\ConfigValidator;
use Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Item\Item as CmsItem;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry;
use Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry;
use Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Layout\Type\Zone;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandlerWithTranslatableParameter;
use Netgen\BlockManager\Tests\Block\Stubs\ContainerDefinition;
use Netgen\BlockManager\Tests\Block\Stubs\ContainerDefinitionHandler;
use Netgen\BlockManager\Tests\Collection\Stubs\ItemDefinition;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use Netgen\BlockManager\Tests\Config\Stubs\Block\HttpCacheConfigHandler;
use Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinition;
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

    public function setUp()
    {
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
            array(
                'identifier' => '4_zones_a',
                'zones' => array(
                    'top' => new Zone(),
                    'left' => new Zone(),
                    'right' => new Zone(array('allowedBlockDefinitions' => array('title', 'list'))),
                    'bottom' => new Zone(array('allowedBlockDefinitions' => array('title'))),
                ),
            )
        );

        $layoutType2 = new LayoutType(
            array(
                'identifier' => '4_zones_b',
                'zones' => array(
                    'top' => new Zone(),
                    'left' => new Zone(),
                    'right' => new Zone(),
                    'bottom' => new Zone(),
                ),
            )
        );

        $this->layoutTypeRegistry = new LayoutTypeRegistry();
        $this->layoutTypeRegistry->addLayoutType('4_zones_a', $layoutType1);
        $this->layoutTypeRegistry->addLayoutType('4_zones_b', $layoutType2);

        $this->itemDefinitionRegistry = new ItemDefinitionRegistry();
        $this->itemDefinitionRegistry->addItemDefinition('ezlocation', new ItemDefinition('ezlocation'));
        $this->itemDefinitionRegistry->addItemDefinition('ezcontent', new ItemDefinition('ezcontent'));

        $this->queryTypeRegistry = new QueryTypeRegistry();
        $this->queryTypeRegistry->addQueryType('ezcontent_search', new QueryType('ezcontent_search'));

        $configDefinition1 = new ConfigDefinition('http_cache', new HttpCacheConfigHandler());

        $blockDefinition1 = new BlockDefinition(
            'title',
            array('small' => array('standard')),
            null,
            false,
            true,
            array('http_cache' => $configDefinition1)
        );

        $blockDefinition2 = new BlockDefinition(
            'text',
            array('standard' => array('standard')),
            null,
            false,
            false,
            array('http_cache' => $configDefinition1)
        );

        $blockDefinition3 = new BlockDefinition(
            'gallery',
            array('standard' => array('standard')),
            new BlockDefinitionHandlerWithTranslatableParameter(),
            true,
            false,
            array('http_cache' => $configDefinition1)
        );

        $blockDefinition4 = new BlockDefinition(
            'list',
            array('standard' => array('standard')),
            new BlockDefinitionHandlerWithTranslatableParameter(),
            true,
            false,
            array('http_cache' => $configDefinition1)
        );

        $blockDefinition5 = new ContainerDefinition(
            'column',
            array('column' => array('standard')),
            new ContainerDefinitionHandler(array(), array('main', 'other'))
        );

        $blockDefinition6 = new ContainerDefinition(
            'two_columns',
            array('two_columns_50_50' => array('standard')),
            new ContainerDefinitionHandler(array(), array('left', 'right'))
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
        $this->targetTypeRegistry->addTargetType(new TargetType('ezcontent'));
        $this->targetTypeRegistry->addTargetType(new TargetType('ezlocation'));
        $this->targetTypeRegistry->addTargetType(new TargetType('ezchildren'));
        $this->targetTypeRegistry->addTargetType(new TargetType('ezsubtree'));
        $this->targetTypeRegistry->addTargetType(new TargetType('ez_semantic_path_info'));
        $this->targetTypeRegistry->addTargetType(new TargetType('ez_semantic_path_info_prefix'));

        $this->conditionTypeRegistry = new ConditionTypeRegistry();
        $this->conditionTypeRegistry->addConditionType(new ConditionType('condition'));
        $this->conditionTypeRegistry->addConditionType(new ConditionType('ez_site_access'));
        $this->conditionTypeRegistry->addConditionType(new ConditionType('route_parameter'));

        $this->parameterTypeRegistry = new ParameterTypeRegistry();
        $this->parameterTypeRegistry->addParameterType(new ParameterType\Compound\BooleanType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\TextLineType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\IntegerType());
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

            $configValidator = new ConfigValidator();
            $configValidator->setValidator($validator);

            $collectionValidator = new CollectionValidator($configValidator);
            $collectionValidator->setValidator($validator);

            $blockValidator = new BlockValidator($configValidator, $collectionValidator);
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

            $configValidator = new ConfigValidator();
            $configValidator->setValidator($validator);

            $collectionValidator = new CollectionValidator($configValidator);
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
            $this->persistenceHandler,
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
        $itemLoaderMock = $this->createMock(ItemLoaderInterface::class);
        $itemLoaderMock
            ->expects($this->any())
            ->method('load')
            ->will($this->returnValue(new CmsItem()));

        return new CollectionMapper(
            $this->persistenceHandler->getCollectionHandler(),
            $this->createParameterMapper(),
            $this->createConfigMapper(),
            $this->itemDefinitionRegistry,
            $this->queryTypeRegistry,
            $itemLoaderMock
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
            $this->persistenceHandler,
            $this->createLayoutMapper(),
            $this->targetTypeRegistry,
            $this->conditionTypeRegistry
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
}
