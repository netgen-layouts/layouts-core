<?php

namespace Netgen\BlockManager\Tests\Core\Service\Doctrine;

use Netgen\BlockManager\Tests\Core\Persistence\Doctrine\TestCase as PersistenceTestCase;
use Netgen\BlockManager\Core\Service\Mapper\BlockMapper;
use Netgen\BlockManager\Core\Service\Mapper\LayoutMapper;
use Netgen\BlockManager\Core\Service\LayoutService;
use Netgen\BlockManager\Core\Service\BlockService;
use PHPUnit_Framework_MockObject_MockObject;

trait TestCase
{
    use PersistenceTestCase;

    protected $persistenceHandler;

    /**
     * Creates a layout service under test.
     *
     * @param \PHPUnit_Framework_MockObject_MockObject $validatorMock
     *
     * @return \Netgen\BlockManager\Core\Service\LayoutService
     */
    protected function createLayoutService(
        PHPUnit_Framework_MockObject_MockObject $validatorMock
    ) {
        $this->persistenceHandler = $this->persistenceHandler ?: $this->createPersistenceHandler();

        return new LayoutService(
            $validatorMock,
            $this->createLayoutMapper(),
            $this->persistenceHandler
        );
    }

    /**
     * Creates a block service under test.
     *
     * @param \PHPUnit_Framework_MockObject_MockObject $validatorMock
     *
     * @return \Netgen\BlockManager\Core\Service\BlockService
     */
    protected function createBlockService(
        PHPUnit_Framework_MockObject_MockObject $validatorMock
    ) {
        $this->persistenceHandler = $this->persistenceHandler ?: $this->createPersistenceHandler();

        return new BlockService(
            $validatorMock,
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
        $this->persistenceHandler = $this->persistenceHandler ?: $this->createPersistenceHandler();

        return new BlockMapper($this->persistenceHandler);
    }

    /**
     * Creates the layout mapper under test.
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper
     */
    protected function createLayoutMapper()
    {
        $this->persistenceHandler = $this->persistenceHandler ?: $this->createPersistenceHandler();

        return new LayoutMapper(
            $this->createBlockMapper(),
            $this->persistenceHandler
        );
    }
}
