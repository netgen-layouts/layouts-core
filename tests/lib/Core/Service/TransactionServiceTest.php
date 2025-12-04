<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Service;

use Exception;
use Netgen\Layouts\Core\Service\TransactionService;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Persistence\TransactionHandlerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(TransactionService::class)]
final class TransactionServiceTest extends TestCase
{
    private MockObject&TransactionHandlerInterface $transactionHandlerMock;

    private TransactionService $service;

    protected function setUp(): void
    {
        $this->transactionHandlerMock = $this->createMock(TransactionHandlerInterface::class);

        $this->service = new TransactionService($this->transactionHandlerMock);
    }

    public function testTransaction(): void
    {
        $this->transactionHandlerMock
            ->expects($this->once())
            ->method('beginTransaction');

        $this->transactionHandlerMock
            ->expects($this->once())
            ->method('commitTransaction');

        $return = $this->service->transaction(
            static fn (): int => 42,
        );

        self::assertSame(42, $return);
    }

    public function testTransactionWithException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception');

        $this->transactionHandlerMock
            ->expects($this->once())
            ->method('beginTransaction');

        $this->transactionHandlerMock
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->service->transaction(
            static function (): void {
                throw new RuntimeException('Test exception');
            },
        );
    }

    public function testBeginTransaction(): void
    {
        $this->transactionHandlerMock
            ->expects($this->once())
            ->method('beginTransaction');

        $this->service->beginTransaction();
    }

    public function testCommitTransaction(): void
    {
        $this->transactionHandlerMock
            ->expects($this->once())
            ->method('commitTransaction');

        $this->service->commitTransaction();
    }

    public function testCommitTransactionThrowsRuntimeException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Test exception text');

        $this->transactionHandlerMock
            ->expects($this->once())
            ->method('commitTransaction')
            ->willThrowException(new RuntimeException('Test exception text'));

        $this->service->commitTransaction();
    }

    public function testRollbackTransaction(): void
    {
        $this->transactionHandlerMock
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->service->rollbackTransaction();
    }

    public function testRollbackTransactionThrowsRuntimeException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Test exception text');

        $this->transactionHandlerMock
            ->expects($this->once())
            ->method('rollbackTransaction')
            ->willThrowException(new RuntimeException('Test exception text'));

        $this->service->rollbackTransaction();
    }
}
