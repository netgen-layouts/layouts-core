<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Doctrine;

use Doctrine\DBAL\Connection;
use Netgen\Layouts\Persistence\Doctrine\TransactionHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(TransactionHandler::class)]
final class TransactionHandlerTest extends TestCase
{
    private Stub&Connection $connectionStub;

    private TransactionHandler $handler;

    protected function setUp(): void
    {
        $this->connectionStub = self::createStub(Connection::class);

        $this->handler = new TransactionHandler($this->connectionStub);
    }

    public function testBeginTransaction(): void
    {
        $this->connectionStub
            ->method('beginTransaction');

        $this->handler->beginTransaction();
    }

    public function testCommitTransaction(): void
    {
        $this->connectionStub
            ->method('commit');

        $this->handler->commitTransaction();
    }

    public function testRollbackTransaction(): void
    {
        $this->connectionStub
            ->method('rollback');

        $this->handler->rollbackTransaction();
    }
}
