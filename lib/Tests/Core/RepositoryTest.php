<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Core\Repository;
use Netgen\BlockManager\Persistence\Handler;
use Exception;

class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $persistenceHandlerMock;

    /**
     * @var \Netgen\BlockManager\API\Repository
     */
    protected $repository;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        $layoutServiceMock = $this->getMock(LayoutService::class);
        $blockServiceMock = $this->getMock(BlockService::class);
        $collectionServiceMock = $this->getMock(CollectionService::class);
        $layoutResolverServiceMock = $this->getMock(LayoutResolverService::class);

        $this->persistenceHandlerMock = $this->getMock(Handler::class);

        $this->repository = new Repository(
            $layoutServiceMock,
            $blockServiceMock,
            $collectionServiceMock,
            $layoutResolverServiceMock,
            $this->persistenceHandlerMock
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Repository::__construct
     * @covers \Netgen\BlockManager\Core\Repository::getLayoutService
     */
    public function testGetLayoutService()
    {
        self::assertInstanceOf(LayoutService::class, $this->repository->getLayoutService());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Repository::getBlockService
     */
    public function testGetBlockService()
    {
        self::assertInstanceOf(BlockService::class, $this->repository->getBlockService());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Repository::getCollectionService
     */
    public function testGetCollectionService()
    {
        self::assertInstanceOf(CollectionService::class, $this->repository->getCollectionService());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Repository::beginTransaction
     */
    public function testBeginTransaction()
    {
        $this->persistenceHandlerMock
            ->expects($this->once())
            ->method('beginTransaction');

        $this->repository->beginTransaction();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Repository::commitTransaction
     */
    public function testCommitTransaction()
    {
        $this->persistenceHandlerMock
            ->expects($this->once())
            ->method('commitTransaction');

        $this->repository->commitTransaction();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Repository::commitTransaction
     * @expectedException \RuntimeException
     */
    public function testCommitTransactionThrowsRuntimeException()
    {
        $this->persistenceHandlerMock
            ->expects($this->once())
            ->method('commitTransaction')
            ->will($this->throwException(new Exception()));

        $this->repository->commitTransaction();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Repository::rollbackTransaction
     */
    public function testRollbackTransaction()
    {
        $this->persistenceHandlerMock
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->repository->rollbackTransaction();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Repository::rollbackTransaction
     * @expectedException \RuntimeException
     */
    public function testRollbackTransactionThrowsRuntimeException()
    {
        $this->persistenceHandlerMock
            ->expects($this->once())
            ->method('rollbackTransaction')
            ->will($this->throwException(new Exception()));

        $this->repository->rollbackTransaction();
    }
}
