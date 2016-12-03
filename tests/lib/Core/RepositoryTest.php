<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Core\Repository;
use Netgen\BlockManager\Persistence\Handler;
use Exception;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
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
        $layoutServiceMock = $this->createMock(LayoutService::class);
        $blockServiceMock = $this->createMock(BlockService::class);
        $collectionServiceMock = $this->createMock(CollectionService::class);
        $layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);

        $this->persistenceHandlerMock = $this->createMock(Handler::class);

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
        $this->assertInstanceOf(LayoutService::class, $this->repository->getLayoutService());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Repository::getBlockService
     */
    public function testGetBlockService()
    {
        $this->assertInstanceOf(BlockService::class, $this->repository->getBlockService());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Repository::getCollectionService
     */
    public function testGetCollectionService()
    {
        $this->assertInstanceOf(CollectionService::class, $this->repository->getCollectionService());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Repository::getLayoutResolverService
     */
    public function testGetLayoutResolverService()
    {
        $this->assertInstanceOf(LayoutResolverService::class, $this->repository->getLayoutResolverService());
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
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
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
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
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
