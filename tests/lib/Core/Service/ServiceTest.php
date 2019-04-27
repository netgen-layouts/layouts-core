<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Service;

use Exception;
use Netgen\Layouts\Core\Service\Service;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Persistence\TransactionHandlerInterface;
use PHPUnit\Framework\TestCase;

final class ServiceTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $transactionHandlerMock;

    /**
     * @var \Netgen\Layouts\Core\Service\Service
     */
    private $service;

    protected function setUp(): void
    {
        $this->transactionHandlerMock = $this->createMock(TransactionHandlerInterface::class);

        $this->service = $this->getMockForAbstractClass(
            Service::class,
            [$this->transactionHandlerMock]
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\Service::__construct
     * @covers \Netgen\Layouts\Core\Service\Service::transaction
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
            static function (): int {
                return 42;
            }
        );

        self::assertSame(42, $return);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\Service::transaction
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
            }
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\Service::beginTransaction
     */
    public function testBeginTransaction(): void
    {
        $this->transactionHandlerMock
            ->expects(self::once())
            ->method('beginTransaction');

        $this->service->beginTransaction();
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\Service::commitTransaction
     */
    public function testCommitTransaction(): void
    {
        $this->transactionHandlerMock
            ->expects(self::once())
            ->method('commitTransaction');

        $this->service->commitTransaction();
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\Service::commitTransaction
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
     * @covers \Netgen\Layouts\Core\Service\Service::rollbackTransaction
     */
    public function testRollbackTransaction(): void
    {
        $this->transactionHandlerMock
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->service->rollbackTransaction();
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\Service::rollbackTransaction
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
