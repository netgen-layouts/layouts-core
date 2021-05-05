<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Service;

use Exception;
use Netgen\Layouts\Core\Service\TransactionService;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Persistence\TransactionHandlerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class TransactionServiceTest extends TestCase
{
    private MockObject $transactionHandlerMock;

    private TransactionService $service;

    protected function setUp(): void
    {
        $this->transactionHandlerMock = $this->createMock(TransactionHandlerInterface::class);

        $this->service = new TransactionService($this->transactionHandlerMock);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\TransactionService::__construct
     * @covers \Netgen\Layouts\Core\Service\TransactionService::transaction
     */
    public function testTransaction(): void
    {
        $this->transactionHandlerMock
            ->expects(self::once())
            ->method('beginTransaction');

        $this->transactionHandlerMock
            ->expects(self::never())
            ->method('rollbackTransaction');

        $this->transactionHandlerMock
            ->expects(self::once())
            ->method('commitTransaction');

        $return = $this->service->transaction(
            static fn (): int => 42,
        );

        self::assertSame(42, $return);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\TransactionService::transaction
     */
    public function testTransactionWithException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception');

        $this->transactionHandlerMock
            ->expects(self::once())
            ->method('beginTransaction');

        $this->transactionHandlerMock
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->transactionHandlerMock
            ->expects(self::never())
            ->method('commitTransaction');

        $this->service->transaction(
            static function (): void {
                throw new RuntimeException('Test exception');
            },
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\TransactionService::beginTransaction
     */
    public function testBeginTransaction(): void
    {
        $this->transactionHandlerMock
            ->expects(self::once())
            ->method('beginTransaction');

        $this->service->beginTransaction();
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\TransactionService::commitTransaction
     */
    public function testCommitTransaction(): void
    {
        $this->transactionHandlerMock
            ->expects(self::once())
            ->method('commitTransaction');

        $this->service->commitTransaction();
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\TransactionService::commitTransaction
     */
    public function testCommitTransactionThrowsRuntimeException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Test exception text');

        $this->transactionHandlerMock
            ->expects(self::once())
            ->method('commitTransaction')
            ->willThrowException(new RuntimeException('Test exception text'));

        $this->service->commitTransaction();
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\TransactionService::rollbackTransaction
     */
    public function testRollbackTransaction(): void
    {
        $this->transactionHandlerMock
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->service->rollbackTransaction();
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\TransactionService::rollbackTransaction
     */
    public function testRollbackTransactionThrowsRuntimeException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Test exception text');

        $this->transactionHandlerMock
            ->expects(self::once())
            ->method('rollbackTransaction')
            ->willThrowException(new RuntimeException('Test exception text'));

        $this->service->rollbackTransaction();
    }
}
