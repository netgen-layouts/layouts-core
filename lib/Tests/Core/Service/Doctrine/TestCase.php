<?php

namespace Netgen\BlockManager\Tests\Core\Service\Doctrine;

use Netgen\BlockManager\Core\Service\Mapper;
use Netgen\BlockManager\Tests\Core\Persistence\Doctrine\TestCase as PersistenceTestCase;
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
            $this->persistenceHandler,
            $this->createMapper()
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
            $this->persistenceHandler,
            $this->createMapper()
        );
    }

    /**
     * Creates the mapper under test.
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper
     */
    protected function createMapper()
    {
        $this->persistenceHandler = $this->persistenceHandler ?: $this->createPersistenceHandler();

        return new Mapper($this->persistenceHandler);
    }
}
