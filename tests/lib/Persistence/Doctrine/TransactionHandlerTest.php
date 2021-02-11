<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Doctrine;

use Doctrine\DBAL\Connection;
use Netgen\Layouts\Persistence\Doctrine\TransactionHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class TransactionHandlerTest extends TestCase
{
    private MockObject $connectionMock;

    private TransactionHandler $handler;

    protected function setUp(): void
    {
        $this->connectionMock = $this->createMock(Connection::class);

        $this->handler = new TransactionHandler($this->connectionMock);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\TransactionHandler::__construct
     * @covers \Netgen\Layouts\Persistence\Doctrine\TransactionHandler::beginTransaction
     */
    public function testBeginTransaction(): void
    {
        $this->connectionMock
            ->expects(self::once())
            ->method('beginTransaction');

        $this->handler->beginTransaction();
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\TransactionHandler::commitTransaction
     */
    public function testCommitTransaction(): void
    {
        $this->connectionMock
            ->expects(self::once())
            ->method('commit');

        $this->handler->commitTransaction();
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\TransactionHandler::rollbackTransaction
     */
    public function testRollbackTransaction(): void
    {
        $this->connectionMock
            ->expects(self::once())
            ->method('rollback');

        $this->handler->rollbackTransaction();
    }
}
