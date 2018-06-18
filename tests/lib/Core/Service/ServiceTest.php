<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Service;

use Exception;
use Netgen\BlockManager\Core\Service\Service;
use Netgen\BlockManager\Persistence\HandlerInterface;
use PHPUnit\Framework\TestCase;

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
            ->expects($this->once())
            ->method('beginTransaction');

        $this->persistenceHandlerMock
            ->expects($this->never())
            ->method('rollbackTransaction');

        $this->persistenceHandlerMock
            ->expects($this->once())
            ->method('commitTransaction');

        $return = $this->service->transaction(
            function (): int {
                return 42;
            }
        );

        $this->assertSame(42, $return);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Service::transaction
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception
     */
    public function testTransactionWithException(): void
    {
        $this->persistenceHandlerMock
            ->expects($this->once())
            ->method('beginTransaction');

        $this->persistenceHandlerMock
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->persistenceHandlerMock
            ->expects($this->never())
            ->method('commitTransaction');

        $this->service->transaction(
            function (): void {
                throw new Exception('Test exception');
            }
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Service::beginTransaction
     */
    public function testBeginTransaction(): void
    {
        $this->persistenceHandlerMock
            ->expects($this->once())
            ->method('beginTransaction');

        $this->service->beginTransaction();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Service::commitTransaction
     */
    public function testCommitTransaction(): void
    {
        $this->persistenceHandlerMock
            ->expects($this->once())
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
            ->expects($this->once())
            ->method('commitTransaction')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->service->commitTransaction();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Service::rollbackTransaction
     */
    public function testRollbackTransaction(): void
    {
        $this->persistenceHandlerMock
            ->expects($this->once())
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
            ->expects($this->once())
            ->method('rollbackTransaction')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->service->rollbackTransaction();
    }
}
