<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Persistence\Doctrine;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;

final class TransactionHandlerTest extends TestCase
{
    use TestCaseTrait;

    /**
     * @var \Doctrine\DBAL\Connection&\PHPUnit\Framework\MockObject\MockObject
     */
    private $connectionMock;

    /**
     * @var \Netgen\BlockManager\Persistence\TransactionHandlerInterface
     */
    private $handler;

    public function setUp(): void
    {
        $this->connectionMock = $this->createMock(Connection::class);

        $this->handler = $this->createTransactionHandler($this->connectionMock);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\TransactionHandler::beginTransaction
     */
    public function testBeginTransaction(): void
    {
        $this->connectionMock
            ->expects(self::once())
            ->method('beginTransaction');

        $this->handler->beginTransaction();
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\TransactionHandler::commitTransaction
     */
    public function testCommitTransaction(): void
    {
        $this->connectionMock
            ->expects(self::once())
            ->method('commit');

        $this->handler->commitTransaction();
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\TransactionHandler::rollbackTransaction
     */
    public function testRollbackTransaction(): void
    {
        $this->connectionMock
            ->expects(self::once())
            ->method('rollback');

        $this->handler->rollbackTransaction();
    }
}
