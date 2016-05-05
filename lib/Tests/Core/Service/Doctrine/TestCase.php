<?php

namespace Netgen\BlockManager\Tests\Core\Service\Doctrine;

use Netgen\BlockManager\Core\Service\Validator\BlockValidator;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCase as PersistenceTestCase;
use Netgen\BlockManager\Core\Service\Mapper\BlockMapper;
use Netgen\BlockManager\Core\Service\Mapper\LayoutMapper;
use Netgen\BlockManager\Core\Service\LayoutService;
use Netgen\BlockManager\Core\Service\BlockService;

trait TestCase
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
            $this->persistenceHandler
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
            $this->persistenceHandler
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
}
