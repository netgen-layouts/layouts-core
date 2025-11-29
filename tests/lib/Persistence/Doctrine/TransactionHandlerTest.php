<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Doctrine;

use Doctrine\DBAL\Connection;
use Netgen\Layouts\Persistence\Doctrine\TransactionHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(TransactionHandler::class)]
final class TransactionHandlerTest extends TestCase
{
    private MockObject&Connection $connectionMock;

    private TransactionHandler $handler;

    protected function setUp(): void
    {
        $this->connectionMock = $this->createMock(Connection::class);

        $this->handler = new TransactionHandler($this->connectionMock);
    }

    public function testBeginTransaction(): void
    {
        $this->connectionMock
            ->expects($this->once())
            ->method('beginTransaction');

        $this->handler->beginTransaction();
    }

    public function testCommitTransaction(): void
    {
        $this->connectionMock
            ->expects($this->once())
            ->method('commit');

        $this->handler->commitTransaction();
    }

    public function testRollbackTransaction(): void
    {
        $this->connectionMock
            ->expects($this->once())
            ->method('rollback');

        $this->handler->rollbackTransaction();
    }
}
