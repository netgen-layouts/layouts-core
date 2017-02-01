<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Exception;
use Netgen\BlockManager\Core\Service\Service;
use Netgen\BlockManager\Persistence\Handler;
use PHPUnit\Framework\TestCase;

class ServiceTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $persistenceHandlerMock;

    /**
     * @var \Netgen\BlockManager\Core\Service\Service
     */
    protected $service;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->persistenceHandlerMock = $this->createMock(Handler::class);

        $this->service = $this->getMockForAbstractClass(
            Service::class,
            array($this->persistenceHandlerMock)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Service::__construct
     * @covers \Netgen\BlockManager\Core\Service\Service::beginTransaction
     */
    public function testBeginTransaction()
    {
        $this->persistenceHandlerMock
            ->expects($this->once())
            ->method('beginTransaction');

        $this->service->beginTransaction();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Service::commitTransaction
     */
    public function testCommitTransaction()
    {
        $this->persistenceHandlerMock
            ->expects($this->once())
            ->method('commitTransaction');

        $this->service->commitTransaction();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Service::commitTransaction
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     */
    public function testCommitTransactionThrowsRuntimeException()
    {
        $this->persistenceHandlerMock
            ->expects($this->once())
            ->method('commitTransaction')
            ->will($this->throwException(new Exception()));

        $this->service->commitTransaction();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Service::rollbackTransaction
     */
    public function testRollbackTransaction()
    {
        $this->persistenceHandlerMock
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->service->rollbackTransaction();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Service::rollbackTransaction
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     */
    public function testRollbackTransactionThrowsRuntimeException()
    {
        $this->persistenceHandlerMock
            ->expects($this->once())
            ->method('rollbackTransaction')
            ->will($this->throwException(new Exception()));

        $this->service->rollbackTransaction();
    }
}
