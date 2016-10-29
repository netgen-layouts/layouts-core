<?php

namespace Netgen\BlockManager\Tests\Core\Service\Doctrine;

use Netgen\BlockManager\Core\Service\CollectionService;
use Netgen\BlockManager\Core\Service\LayoutResolverService;
use Netgen\BlockManager\Core\Service\Mapper\CollectionMapper;
use Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper;
use Netgen\BlockManager\Core\Service\Validator\BlockValidator;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait as PersistenceTestCase;
use Netgen\BlockManager\Core\Service\Mapper\BlockMapper;
use Netgen\BlockManager\Core\Service\Mapper\LayoutMapper;
use Netgen\BlockManager\Core\Service\LayoutService;
use Netgen\BlockManager\Core\Service\BlockService;

trait TestCaseTrait
{
    use PersistenceTestCase;

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
     *
     * @return \Netgen\BlockManager\Core\Service\LayoutService
     */
    protected function createLayoutService(LayoutValidator $validator)
    {
        return new LayoutService(
            $validator,
            $this->createLayoutMapper(),
            $this->persistenceHandler,
            $this->layoutTypeRegistry
        );
    }

    /**
     * Creates a block service under test.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\BlockValidator $validator
     *
     * @return \Netgen\BlockManager\Core\Service\BlockService
     */
    protected function createBlockService(BlockValidator $validator)
    {
        return new BlockService(
            $validator,
            $this->createBlockMapper(),
            $this->persistenceHandler,
            $this->layoutTypeRegistry,
            $this->blockDefinitionRegistry
        );
    }

    /**
     * Creates a collection service under test.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\CollectionValidator $validator
     *
     * @return \Netgen\BlockManager\Core\Service\CollectionService
     */
    protected function createCollectionService(CollectionValidator $validator)
    {
        return new CollectionService(
            $validator,
            $this->createCollectionMapper(),
            $this->persistenceHandler,
            $this->queryTypeRegistry
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
            $this->createCollectionMapper(),
            $this->blockDefinitionRegistry
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
            $this->persistenceHandler,
            $this->layoutTypeRegistry
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
            $this->persistenceHandler,
            $this->queryTypeRegistry
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
            $this->createLayoutMapper(),
            $this->targetTypeRegistry,
            $this->conditionTypeRegistry
        );
    }
}
