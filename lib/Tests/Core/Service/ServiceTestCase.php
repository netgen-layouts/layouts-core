<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\Core\Service\Validator\BlockValidator;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry;
use Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry;
use Netgen\BlockManager\Tests\Configuration\Stubs\LayoutType;
use Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistry;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistry;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\ConditionType;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\TargetType;
use PHPUnit\Framework\TestCase;

abstract class ServiceTestCase extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface
     */
    protected $layoutTypeRegistry;

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
    }

    /**
     * Prepares the registries used in tests.
     */
    protected function prepareRegistries()
    {
        $layoutType1 = new LayoutType(
            '4_zones_a',
            array(
                'top' => array(),
                'left' => array(),
                'right' => array('title', 'list'),
                'bottom' => array('title'),
            )
        );

        $layoutType2 = new LayoutType(
            '4_zones_b',
            array(
                'top' => array(),
                'left' => array(),
                'right' => array(),
                'bottom' => array(),
            )
        );

        $this->layoutTypeRegistry = new LayoutTypeRegistry();
        $this->layoutTypeRegistry->addLayoutType($layoutType1);
        $this->layoutTypeRegistry->addLayoutType($layoutType2);

        $this->queryTypeRegistry = new QueryTypeRegistry();
        $this->queryTypeRegistry->addQueryType(new QueryType('ezcontent_search'));

        $blockDefinition1 = new BlockDefinition('title', array('small' => array('standard')));
        $blockDefinition2 = new BlockDefinition('text', array('standard' => array('standard')));
        $blockDefinition3 = new BlockDefinition('gallery', array('standard' => array('standard')));
        $blockDefinition4 = new BlockDefinition('list', array('standard' => array('standard')));

        $this->blockDefinitionRegistry = new BlockDefinitionRegistry();
        $this->blockDefinitionRegistry->addBlockDefinition($blockDefinition1);
        $this->blockDefinitionRegistry->addBlockDefinition($blockDefinition2);
        $this->blockDefinitionRegistry->addBlockDefinition($blockDefinition3);
        $this->blockDefinitionRegistry->addBlockDefinition($blockDefinition4);

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
        $this->parameterTypeRegistry->addParameterType(new ParameterType\TextLineType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\IntegerType());
    }

    /**
     * Creates a layout service under test.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\LayoutValidator $validator
     *
     * @return \Netgen\BlockManager\Core\Service\LayoutService
     */
    abstract protected function createLayoutService(LayoutValidator $validator);

    /**
     * Creates a block service under test.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\BlockValidator $validator
     *
     * @return \Netgen\BlockManager\API\Service\BlockService
     */
    abstract protected function createBlockService(BlockValidator $validator);

    /**
     * Creates a collection service under test.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\CollectionValidator $validator
     *
     * @return \Netgen\BlockManager\API\Service\CollectionService
     */
    abstract protected function createCollectionService(CollectionValidator $validator);

    /**
     * Creates a layout resolver service under test.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator $validator
     *
     * @return \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    abstract protected function createLayoutResolverService(LayoutResolverValidator $validator);

    /**
     * Creates a layout mapper under test.
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper
     */
    abstract protected function createLayoutMapper();

    /**
     * Creates a block mapper under test.
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper\BlockMapper
     */
    abstract protected function createBlockMapper();

    /**
     * Creates a collection mapper under test.
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper
     */
    abstract protected function createCollectionMapper();

    /**
     * Creates a layout resolver mapper under test.
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper
     */
    abstract protected function createLayoutResolverMapper();

    /**
     * Creates the parameter mapper under test.
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper
     */
    abstract protected function createParameterMapper();
}
