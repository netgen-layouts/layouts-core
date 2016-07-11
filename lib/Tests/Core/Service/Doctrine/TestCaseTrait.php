<?php

namespace Netgen\BlockManager\Tests\Core\Service\Doctrine;

use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface;
use Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface;
use Netgen\BlockManager\Core\Service\CollectionService;
use Netgen\BlockManager\Core\Service\LayoutResolverService;
use Netgen\BlockManager\Core\Service\Mapper\CollectionMapper;
use Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper;
use Netgen\BlockManager\Core\Service\Validator\BlockValidator;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait as PersistenceTestCaseTrait;
use Netgen\BlockManager\Core\Service\Mapper\BlockMapper;
use Netgen\BlockManager\Core\Service\Mapper\LayoutMapper;
use Netgen\BlockManager\Core\Service\LayoutService;
use Netgen\BlockManager\Core\Service\BlockService;

trait TestCaseTrait
{
    use PersistenceTestCaseTrait;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler
     */
    protected $persistenceHandler;

    /**
     * Prepares the prerequisites for using services in tests.
     */
    public function preparePersistence()
    {
        $this->prepareHandlers();

        $this->persistenceHandler = $this->createPersistenceHandler();
    }

    /**
     * Creates a layout service under test.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\LayoutValidator $validator
     * @param \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface $layoutTypeRegistry
     *
     * @return \Netgen\BlockManager\Core\Service\LayoutService
     */
    protected function createLayoutService(LayoutValidator $validator, LayoutTypeRegistryInterface $layoutTypeRegistry)
    {
        return new LayoutService(
            $validator,
            $this->createLayoutMapper(),
            $this->persistenceHandler,
            $layoutTypeRegistry
        );
    }

    /**
     * Creates a block service under test.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\BlockValidator $validator
     * @param \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface $layoutTypeRegistry
     * @param \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface $blockDefinitionRegistry
     *
     * @return \Netgen\BlockManager\Core\Service\BlockService
     */
    protected function createBlockService(
        BlockValidator $validator,
        LayoutTypeRegistryInterface $layoutTypeRegistry,
        BlockDefinitionRegistryInterface $blockDefinitionRegistry
    ) {
        return new BlockService(
            $validator,
            $this->createBlockMapper(),
            $this->persistenceHandler,
            $layoutTypeRegistry,
            $blockDefinitionRegistry
        );
    }

    /**
     * Creates a collection service under test.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\CollectionValidator $validator
     * @param \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface $queryTypeRegistry
     *
     * @return \Netgen\BlockManager\Core\Service\CollectionService
     */
    protected function createCollectionService(CollectionValidator $validator, QueryTypeRegistryInterface $queryTypeRegistry)
    {
        return new CollectionService(
            $queryTypeRegistry,
            $validator,
            $this->createCollectionMapper(),
            $this->persistenceHandler
        );
    }

    /**
     * Creates a layout resolver service under test.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator $validator
     *
     * @return \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    protected function createLayoutResolverService(LayoutResolverValidator $validator)
    {
        return new LayoutResolverService(
            $validator,
            $this->createLayoutResolverMapper(),
            $this->persistenceHandler
        );
    }

    /**
     * Creates the block mapper under test.
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper\BlockMapper
     */
    protected function createBlockMapper()
    {
        return new BlockMapper(
            $this->persistenceHandler,
            $this->createCollectionMapper()
        );
    }

    /**
     * Creates the layout mapper under test.
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper
     */
    protected function createLayoutMapper()
    {
        return new LayoutMapper(
            $this->createBlockMapper(),
            $this->persistenceHandler
        );
    }

    /**
     * Creates the collection mapper under test.
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper
     */
    protected function createCollectionMapper()
    {
        return new CollectionMapper(
            $this->persistenceHandler
        );
    }

    /**
     * Creates the collection mapper under test.
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper
     */
    protected function createLayoutResolverMapper()
    {
        return new LayoutResolverMapper(
            $this->persistenceHandler,
            $this->createLayoutMapper()
        );
    }
}
