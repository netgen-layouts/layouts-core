<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Error;

use Exception;
use Netgen\BlockManager\Error\DebugErrorHandler;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class DebugErrorHandlerTest extends TestCase
{
    /**
     * @var \Psr\Log\LoggerInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $loggerMock;

    /**
     * @var \Netgen\BlockManager\Error\DebugErrorHandler
     */
    private $errorHandler;

    public function setUp(): void
    {
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->errorHandler = new DebugErrorHandler($this->loggerMock);
    }

    /**
     * @covers \Netgen\BlockManager\Error\DebugErrorHandler::__construct
     * @covers \Netgen\BlockManager\Error\DebugErrorHandler::handleError
     * @covers \Netgen\BlockManager\Error\DebugErrorHandler::logError
     */
    public function testHandleError(): void
    {
        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                $this->equalTo('Test message'),
                ['error' => $exception]
            );

        $this->errorHandler->handleError($exception);
    }

    /**
     * @covers \Netgen\BlockManager\Error\DebugErrorHandler::handleError
     * @covers \Netgen\BlockManager\Error\DebugErrorHandler::logError
     */
    public function testHandleErrorWithCustomMessage(): void
    {
        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                $this->equalTo('Custom message'),
                ['error' => $exception]
            );

        $this->errorHandler->handleError($exception, 'Custom message');
    }

    /**
     * @covers \Netgen\BlockManager\Error\DebugErrorHandler::handleError
     * @covers \Netgen\BlockManager\Error\DebugErrorHandler::logError
     */
    public function testHandleErrorWithEmptyMessage(): void
    {
        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                $this->equalTo(''),
                ['error' => $exception]
            );

        $this->errorHandler->handleError($exception, '');
    }

    /**
     * @covers \Netgen\BlockManager\Error\DebugErrorHandler::handleError
     * @covers \Netgen\BlockManager\Error\DebugErrorHandler::logError
     */
    public function testHandleErrorWithContext(): void
    {
        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                $this->equalTo('Test message'),
                ['value' => 42, 'error' => $exception]
            );

        $this->errorHandler->handleError($exception, null, ['value' => 42]);
    }

    /**
     * @covers \Netgen\BlockManager\Error\DebugErrorHandler::handleError
     * @covers \Netgen\BlockManager\Error\DebugErrorHandler::logError
     * @expectedException \Exception
     * @expectedExceptionMessage Test message
     */
    public function testHandleErrorThrowsError(): void
    {
        $this->errorHandler = new DebugErrorHandler($this->loggerMock, true);

        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                $this->equalTo('Test message'),
                ['error' => $exception]
            );

        $this->errorHandler->handleError($exception);
    }

    /**
     * @covers \Netgen\BlockManager\Error\DebugErrorHandler::handleError
     * @covers \Netgen\BlockManager\Error\DebugErrorHandler::logError
     * @expectedException \Exception
     * @expectedExceptionMessage Test message
     */
    public function testHandleErrorThrowsErrorWithCustomMessage(): void
    {
        $this->errorHandler = new DebugErrorHandler($this->loggerMock, true);

        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                $this->equalTo('Custom message'),
                ['error' => $exception]
            );

        $this->errorHandler->handleError($exception, 'Custom message');
    }

    /**
     * @covers \Netgen\BlockManager\Error\DebugErrorHandler::handleError
     * @covers \Netgen\BlockManager\Error\DebugErrorHandler::logError
     * @expectedException \Exception
     * @expectedExceptionMessage Test message
     */
    public function testHandleErrorThrowsErrorWithEmptyMessage(): void
    {
        $this->errorHandler = new DebugErrorHandler($this->loggerMock, true);

        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                $this->equalTo(''),
                ['error' => $exception]
            );

        $this->errorHandler->handleError($exception, '');
    }

    /**
     * @covers \Netgen\BlockManager\Error\DebugErrorHandler::handleError
     * @covers \Netgen\BlockManager\Error\DebugErrorHandler::logError
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Test message
     */
    public function testHandleErrorThrowsErrorWithContext(): void
    {
        $this->errorHandler = new DebugErrorHandler($this->loggerMock, true);

        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                $this->equalTo('Test message'),
                ['value' => 42, 'error' => $exception]
            );

        $this->errorHandler->handleError($exception, null, ['value' => 42]);
    }
}
