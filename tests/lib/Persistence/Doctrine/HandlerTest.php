<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Persistence\Doctrine;

use Doctrine\DBAL\Connection;
use Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler;
use Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler;
use Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler;
use Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler;
use PHPUnit\Framework\TestCase;

final class HandlerTest extends TestCase
{
    use TestCaseTrait;

    /**
     * @var \Doctrine\DBAL\Connection&\PHPUnit\Framework\MockObject\MockObject
     */
    private $connectionMock;

    /**
     * @var \Netgen\BlockManager\Persistence\HandlerInterface
     */
    private $handler;

    public function setUp(): void
    {
        $this->connectionMock = $this->createMock(Connection::class);

        $this->handler = $this->createPersistenceHandler($this->connectionMock);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler::getLayoutHandler
     */
    public function testGetLayoutHandler(): void
    {
        $this->assertInstanceOf(
            LayoutHandler::class,
            $this->handler->getLayoutHandler()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler::getBlockHandler
     */
    public function testGetBlockHandler(): void
    {
        $this->assertInstanceOf(
            BlockHandler::class,
            $this->handler->getBlockHandler()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler::getCollectionHandler
     */
    public function testGetCollectionHandler(): void
    {
        $this->assertInstanceOf(
            CollectionHandler::class,
            $this->handler->getCollectionHandler()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler::getLayoutResolverHandler
     */
    public function testGetLayoutResolverHandler(): void
    {
        $this->assertInstanceOf(
            LayoutResolverHandler::class,
            $this->handler->getLayoutResolverHandler()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler::beginTransaction
     */
    public function testBeginTransaction(): void
    {
        $this->connectionMock
            ->expects($this->once())
            ->method('beginTransaction');

        $this->handler->beginTransaction();
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler::commitTransaction
     */
    public function testCommitTransaction(): void
    {
        $this->connectionMock
            ->expects($this->once())
            ->method('commit');

        $this->handler->commitTransaction();
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler::rollbackTransaction
     */
    public function testRollbackTransaction(): void
    {
        $this->connectionMock
            ->expects($this->once())
            ->method('rollback');

        $this->handler->rollbackTransaction();
    }
}
