<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\Core\Service\Validator\BlockValidator;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Configuration\LayoutType\LayoutType;
use Netgen\BlockManager\Configuration\LayoutType\Zone as LayoutTypeZone;
use Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistry;
use Netgen\BlockManager\Collection\QueryType;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryTypeHandler;
use Netgen\BlockManager\Collection\QueryType\Configuration\Configuration;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistry;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry;
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
        $layoutType = new LayoutType(
            '4_zones_a',
            true,
            '4 zones A',
            array(
                'top' => new LayoutTypeZone('top', 'Top', array()),
                'left' => new LayoutTypeZone('left', 'Left', array()),
                'right' => new LayoutTypeZone('right', 'Right', array('title', 'list')),
                'bottom' => new LayoutTypeZone('bottom', 'Bottom', array('title')),
            )
        );

        $this->layoutTypeRegistry = new LayoutTypeRegistry();
        $this->layoutTypeRegistry->addLayoutType($layoutType);

        $this->queryTypeRegistry = new QueryTypeRegistry();
        $this->queryTypeRegistry->addQueryType(
            new QueryType(
                'ezcontent_search',
                new QueryTypeHandler(),
                new Configuration('query_type', 'Query type')
            )
        );

        $blockDefinition1 = new BlockDefinition('title', array('small' => array('standard')));
        $blockDefinition2 = new BlockDefinition('text', array('standard' => array('standard')));
        $blockDefinition3 = new BlockDefinition('gallery', array('standard' => array('standard')));
        $blockDefinition4 = new BlockDefinition('list', array('standard' => array('standard')));

        $this->blockDefinitionRegistry = new BlockDefinitionRegistry();
        $this->blockDefinitionRegistry->addBlockDefinition($blockDefinition1);
        $this->blockDefinitionRegistry->addBlockDefinition($blockDefinition2);
        $this->blockDefinitionRegistry->addBlockDefinition($blockDefinition3);
        $this->blockDefinitionRegistry->addBlockDefinition($blockDefinition4);
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
}
