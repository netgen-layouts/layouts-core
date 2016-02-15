<?php

namespace Netgen\BlockManager\Tests\Core\Persistence\Doctrine;

use Netgen\BlockManager\Core\Persistence\Doctrine\Handler;
use Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler as LayoutHandler;
use Doctrine\DBAL\Connection;

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
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler::__construct
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler::getLayoutHandler
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
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler::beginTransaction
     */
    public function testBeginTransaction()
    {
        $databaseConnection = $this
            ->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $databaseConnection
            ->expects($this->once())
            ->method('beginTransaction');

        $handler = new Handler(
            $databaseConnection,
            $this->createLayoutHandler()
        );

        $handler->beginTransaction();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler::commitTransaction
     */
    public function testCommitTransaction()
    {
        $databaseConnection = $this
            ->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $databaseConnection
            ->expects($this->once())
            ->method('commit');

        $handler = new Handler(
            $databaseConnection,
            $this->createLayoutHandler()
        );

        $handler->commitTransaction();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler::rollbackTransaction
     */
    public function testRollbackTransaction()
    {
        $databaseConnection = $this
            ->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $databaseConnection
            ->expects($this->once())
            ->method('rollback');

        $handler = new Handler(
            $databaseConnection,
            $this->createLayoutHandler()
        );

        $handler->rollbackTransaction();
    }
}
