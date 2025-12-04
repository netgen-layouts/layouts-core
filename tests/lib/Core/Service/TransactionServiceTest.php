<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Service;

use Exception;
use Netgen\Layouts\Core\Service\TransactionService;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Persistence\TransactionHandlerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(TransactionService::class)]
final class TransactionServiceTest extends TestCase
{
    private Stub&TransactionHandlerInterface $transactionHandlerStub;

    private TransactionService $service;

    protected function setUp(): void
    {
        $this->transactionHandlerStub = self::createStub(TransactionHandlerInterface::class);

        $this->service = new TransactionService($this->transactionHandlerStub);
    }

    public function testTransaction(): void
    {
        $this->transactionHandlerStub
            ->method('beginTransaction');

        $this->transactionHandlerStub
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

        $this->transactionHandlerStub
            ->method('beginTransaction');

        $this->transactionHandlerStub
            ->method('rollbackTransaction');

        $this->service->transaction(
            static function (): void {
                throw new RuntimeException('Test exception');
            },
        );
    }

    public function testBeginTransaction(): void
    {
        $this->transactionHandlerStub
            ->method('beginTransaction');

        $this->service->beginTransaction();
    }

    public function testCommitTransaction(): void
    {
        $this->transactionHandlerStub
            ->method('commitTransaction');

        $this->service->commitTransaction();
    }

    public function testCommitTransactionThrowsRuntimeException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Test exception text');

        $this->transactionHandlerStub
            ->method('commitTransaction')
            ->willThrowException(new RuntimeException('Test exception text'));

        $this->service->commitTransaction();
    }

    public function testRollbackTransaction(): void
    {
        $this->transactionHandlerStub
            ->method('rollbackTransaction');

        $this->service->rollbackTransaction();
    }

    public function testRollbackTransactionThrowsRuntimeException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Test exception text');

        $this->transactionHandlerStub
            ->method('rollbackTransaction')
            ->willThrowException(new RuntimeException('Test exception text'));

        $this->service->rollbackTransaction();
    }
}
