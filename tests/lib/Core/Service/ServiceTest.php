<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Service;

use Exception;
use Netgen\BlockManager\Core\Service\Service;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Persistence\TransactionHandlerInterface;
use PHPUnit\Framework\TestCase;

final class ServiceTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $transactionHandlerMock;

    /**
     * @var \Netgen\BlockManager\Core\Service\Service
     */
    private $service;

    public function setUp(): void
    {
        $this->transactionHandlerMock = $this->createMock(TransactionHandlerInterface::class);

        $this->service = $this->getMockForAbstractClass(
            Service::class,
            [$this->transactionHandlerMock]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Service::__construct
     * @covers \Netgen\BlockManager\Core\Service\Service::transaction
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
            function (): int {
                return 42;
            }
        );

        self::assertSame(42, $return);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Service::transaction
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
            function (): void {
                throw new RuntimeException('Test exception');
            }
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Service::beginTransaction
     */
    public function testBeginTransaction(): void
    {
        $this->transactionHandlerMock
            ->expects(self::once())
            ->method('beginTransaction');

        $this->service->beginTransaction();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Service::commitTransaction
     */
    public function testCommitTransaction(): void
    {
        $this->transactionHandlerMock
            ->expects(self::once())
            ->method('commitTransaction');

        $this->service->commitTransaction();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Service::commitTransaction
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
     * @covers \Netgen\BlockManager\Core\Service\Service::rollbackTransaction
     */
    public function testRollbackTransaction(): void
    {
        $this->transactionHandlerMock
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->service->rollbackTransaction();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Service::rollbackTransaction
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
