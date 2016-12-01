<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine;

use Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler;
use Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler;
use Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler;
use Doctrine\DBAL\Connection;
use Netgen\BlockManager\Persistence\Handler\LayoutResolverHandler;
use PHPUnit\Framework\TestCase;

class HandlerTest extends TestCase
{
    use TestCaseTrait;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Handler
     */
    protected $handler;

    /**
     * Sets up the database connection.
     */
    protected function setUp()
    {
        $this->databaseConnection = $this->createMock(Connection::class);

        $this->handler = $this->createPersistenceHandler($this->databaseConnection);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler::getLayoutHandler
     */
    public function testGetLayoutHandler()
    {
        $this->assertInstanceOf(
            LayoutHandler::class,
            $this->handler->getLayoutHandler()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler::getBlockHandler
     */
    public function testGetBlockHandler()
    {
        $this->assertInstanceOf(
            BlockHandler::class,
            $this->handler->getBlockHandler()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler::getCollectionHandler
     */
    public function testGetCollectionHandler()
    {
        $this->assertInstanceOf(
            CollectionHandler::class,
            $this->handler->getCollectionHandler()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler::getLayoutResolverHandler
     */
    public function testGetLayoutResolverHandler()
    {
        $this->assertInstanceOf(
            LayoutResolverHandler::class,
            $this->handler->getLayoutResolverHandler()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler::beginTransaction
     */
    public function testBeginTransaction()
    {
        $this->databaseConnection
            ->expects($this->once())
            ->method('beginTransaction');

        $this->handler->beginTransaction();
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler::commitTransaction
     */
    public function testCommitTransaction()
    {
        $this->databaseConnection
            ->expects($this->once())
            ->method('commit');

        $this->handler->commitTransaction();
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler::rollbackTransaction
     */
    public function testRollbackTransaction()
    {
        $this->databaseConnection
            ->expects($this->once())
            ->method('rollback');

        $this->handler->rollbackTransaction();
    }
}
