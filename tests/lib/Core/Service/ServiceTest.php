<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\Core\Service\Service;
use Netgen\BlockManager\Persistence\HandlerInterface;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ServiceTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $persistenceHandlerMock;

    /**
     * @var \Netgen\BlockManager\Core\Service\Service
     */
    private $service;

    public function setUp(): void
    {
        $this->persistenceHandlerMock = $this->createMock(HandlerInterface::class);

        $this->service = $this->getMockForAbstractClass(
            Service::class,
            [$this->persistenceHandlerMock]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Service::__construct
     * @covers \Netgen\BlockManager\Core\Service\Service::transaction
     */
    public function testTransaction(): void
    {
        $this->persistenceHandlerMock
            ->expects(self::once())
            ->method('beginTransaction');

        $this->persistenceHandlerMock
            ->expects(self::never())
            ->method('rollbackTransaction');

        $this->persistenceHandlerMock
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
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception
     */
    public function testTransactionWithException(): void
    {
        $this->persistenceHandlerMock
            ->expects(self::once())
            ->method('beginTransaction');

        $this->persistenceHandlerMock
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->persistenceHandlerMock
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
        $this->persistenceHandlerMock
            ->expects(self::once())
            ->method('beginTransaction');

        $this->service->beginTransaction();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Service::commitTransaction
     */
    public function testCommitTransaction(): void
    {
        $this->persistenceHandlerMock
            ->expects(self::once())
            ->method('commitTransaction');

        $this->service->commitTransaction();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Service::commitTransaction
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Test exception text
     */
    public function testCommitTransactionThrowsRuntimeException(): void
    {
        $this->persistenceHandlerMock
            ->expects(self::once())
            ->method('commitTransaction')
            ->will(self::throwException(new RuntimeException('Test exception text')));

        $this->service->commitTransaction();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Service::rollbackTransaction
     */
    public function testRollbackTransaction(): void
    {
        $this->persistenceHandlerMock
            ->expects(self::once())
            ->method('rollbackTransaction');

        $this->service->rollbackTransaction();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Service::rollbackTransaction
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Test exception text
     */
    public function testRollbackTransactionThrowsRuntimeException(): void
    {
        $this->persistenceHandlerMock
            ->expects(self::once())
            ->method('rollbackTransaction')
            ->will(self::throwException(new RuntimeException('Test exception text')));

        $this->service->rollbackTransaction();
    }
}
