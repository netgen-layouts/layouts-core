<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine;

use Netgen\BlockManager\Persistence\Doctrine\Handler;
use Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler;
use Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler;
use Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler;
use Doctrine\DBAL\Connection;
use Netgen\BlockManager\Persistence\Handler\LayoutResolverHandler;

class HandlerTest extends \PHPUnit_Framework_TestCase
{
    use TestCase;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->prepareHandlers();
    }

    /**
     * Tears down the tests.
     */
    public function tearDown()
    {
        $this->closeDatabaseConnection();
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler::getLayoutHandler
     */
    public function testGetLayoutHandler()
    {
        $handler = $this->createPersistenceHandler();

        self::assertInstanceOf(
            LayoutHandler::class,
            $handler->getLayoutHandler()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler::getBlockHandler
     */
    public function testGetBlockHandler()
    {
        $handler = $this->createPersistenceHandler();

        self::assertInstanceOf(
            BlockHandler::class,
            $handler->getBlockHandler()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler::getCollectionHandler
     */
    public function testGetCollectionHandler()
    {
        $handler = $this->createPersistenceHandler();

        self::assertInstanceOf(
            CollectionHandler::class,
            $handler->getCollectionHandler()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler::getLayoutResolverHandler
     */
    public function testGetLayoutResolverHandler()
    {
        $handler = $this->createPersistenceHandler();

        self::assertInstanceOf(
            LayoutResolverHandler::class,
            $handler->getLayoutResolverHandler()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler::beginTransaction
     */
    public function testBeginTransaction()
    {
        $databaseConnection = $this
            ->createMock(Connection::class);

        $databaseConnection
            ->expects($this->once())
            ->method('beginTransaction');

        $handler = new Handler(
            $databaseConnection,
            $this->createLayoutHandler(),
            $this->createBlockHandler(),
            $this->createCollectionHandler(),
            $this->createLayoutResolverHandler()
        );

        $handler->beginTransaction();
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler::commitTransaction
     */
    public function testCommitTransaction()
    {
        $databaseConnection = $this
            ->createMock(Connection::class);

        $databaseConnection
            ->expects($this->once())
            ->method('commit');

        $handler = new Handler(
            $databaseConnection,
            $this->createLayoutHandler(),
            $this->createBlockHandler(),
            $this->createCollectionHandler(),
            $this->createLayoutResolverHandler()
        );

        $handler->commitTransaction();
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler::rollbackTransaction
     */
    public function testRollbackTransaction()
    {
        $databaseConnection = $this
            ->createMock(Connection::class);

        $databaseConnection
            ->expects($this->once())
            ->method('rollback');

        $handler = new Handler(
            $databaseConnection,
            $this->createLayoutHandler(),
            $this->createBlockHandler(),
            $this->createCollectionHandler(),
            $this->createLayoutResolverHandler()
        );

        $handler->rollbackTransaction();
    }
}
