<?php

namespace Netgen\BlockManager\Tests\Core\Persistence\Doctrine;

use Netgen\BlockManager\Core\Persistence\Doctrine\Handler;

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
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler::__construct
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler::getBlockHandler
     */
    public function testGetBlockHandler()
    {
        $handler = $this->createPersistenceHandler();

        self::assertInstanceOf(
            'Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler',
            $handler->getBlockHandler()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler::__construct
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler::getLayoutHandler
     */
    public function testGetLayoutHandler()
    {
        $handler = $this->createPersistenceHandler();

        self::assertInstanceOf(
            'Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler',
            $handler->getLayoutHandler()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler::beginTransaction
     */
    public function testBeginTransaction()
    {
        $databaseConnection = $this
            ->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $databaseConnection
            ->expects($this->once())
            ->method('beginTransaction');

        $handler = new Handler(
            $databaseConnection,
            $this->createBlockHandler(),
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
            ->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $databaseConnection
            ->expects($this->once())
            ->method('commit');

        $handler = new Handler(
            $databaseConnection,
            $this->createBlockHandler(),
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
            ->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $databaseConnection
            ->expects($this->once())
            ->method('rollback');

        $handler = new Handler(
            $databaseConnection,
            $this->createBlockHandler(),
            $this->createLayoutHandler()
        );

        $handler->rollbackTransaction();
    }
}
