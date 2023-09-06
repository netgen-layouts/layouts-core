<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Error;

use Exception;
use Netgen\Layouts\Error\DebugErrorHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class DebugErrorHandlerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&\Psr\Log\LoggerInterface
     */
    private MockObject $loggerMock;

    private DebugErrorHandler $errorHandler;

    protected function setUp(): void
    {
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->errorHandler = new DebugErrorHandler($this->loggerMock);
    }

    /**
     * @covers \Netgen\Layouts\Error\DebugErrorHandler::__construct
     * @covers \Netgen\Layouts\Error\DebugErrorHandler::handleError
     * @covers \Netgen\Layouts\Error\DebugErrorHandler::logError
     */
    public function testHandleError(): void
    {
        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects(self::once())
            ->method('critical')
            ->with(
                self::identicalTo('Test message'),
                self::identicalTo(['error' => $exception]),
            );

        $this->errorHandler->handleError($exception);
    }

    /**
     * @covers \Netgen\Layouts\Error\DebugErrorHandler::handleError
     * @covers \Netgen\Layouts\Error\DebugErrorHandler::logError
     */
    public function testHandleErrorWithCustomMessage(): void
    {
        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects(self::once())
            ->method('critical')
            ->with(
                self::identicalTo('Custom message'),
                self::identicalTo(['error' => $exception]),
            );

        $this->errorHandler->handleError($exception, 'Custom message');
    }

    /**
     * @covers \Netgen\Layouts\Error\DebugErrorHandler::handleError
     * @covers \Netgen\Layouts\Error\DebugErrorHandler::logError
     */
    public function testHandleErrorWithEmptyMessage(): void
    {
        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects(self::once())
            ->method('critical')
            ->with(
                self::identicalTo(''),
                self::identicalTo(['error' => $exception]),
            );

        $this->errorHandler->handleError($exception, '');
    }

    /**
     * @covers \Netgen\Layouts\Error\DebugErrorHandler::handleError
     * @covers \Netgen\Layouts\Error\DebugErrorHandler::logError
     */
    public function testHandleErrorWithContext(): void
    {
        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects(self::once())
            ->method('critical')
            ->with(
                self::identicalTo('Test message'),
                self::identicalTo(['value' => 42, 'error' => $exception]),
            );

        $this->errorHandler->handleError($exception, null, ['value' => 42]);
    }

    /**
     * @covers \Netgen\Layouts\Error\DebugErrorHandler::handleError
     * @covers \Netgen\Layouts\Error\DebugErrorHandler::logError
     */
    public function testHandleErrorThrowsError(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test message');

        $this->errorHandler = new DebugErrorHandler($this->loggerMock, true);

        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects(self::once())
            ->method('critical')
            ->with(
                self::identicalTo('Test message'),
                self::identicalTo(['error' => $exception]),
            );

        $this->errorHandler->handleError($exception);
    }

    /**
     * @covers \Netgen\Layouts\Error\DebugErrorHandler::handleError
     * @covers \Netgen\Layouts\Error\DebugErrorHandler::logError
     */
    public function testHandleErrorThrowsErrorWithCustomMessage(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test message');

        $this->errorHandler = new DebugErrorHandler($this->loggerMock, true);

        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects(self::once())
            ->method('critical')
            ->with(
                self::identicalTo('Custom message'),
                self::identicalTo(['error' => $exception]),
            );

        $this->errorHandler->handleError($exception, 'Custom message');
    }

    /**
     * @covers \Netgen\Layouts\Error\DebugErrorHandler::handleError
     * @covers \Netgen\Layouts\Error\DebugErrorHandler::logError
     */
    public function testHandleErrorThrowsErrorWithEmptyMessage(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test message');

        $this->errorHandler = new DebugErrorHandler($this->loggerMock, true);

        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects(self::once())
            ->method('critical')
            ->with(
                self::identicalTo(''),
                self::identicalTo(['error' => $exception]),
            );

        $this->errorHandler->handleError($exception, '');
    }

    /**
     * @covers \Netgen\Layouts\Error\DebugErrorHandler::handleError
     * @covers \Netgen\Layouts\Error\DebugErrorHandler::logError
     */
    public function testHandleErrorThrowsErrorWithContext(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test message');

        $this->errorHandler = new DebugErrorHandler($this->loggerMock, true);

        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects(self::once())
            ->method('critical')
            ->with(
                self::identicalTo('Test message'),
                self::identicalTo(['value' => 42, 'error' => $exception]),
            );

        $this->errorHandler->handleError($exception, null, ['value' => 42]);
    }

    /**
     * @covers \Netgen\Layouts\Error\DebugErrorHandler::__construct
     * @covers \Netgen\Layouts\Error\DebugErrorHandler::logError
     * @covers \Netgen\Layouts\Error\DebugErrorHandler::logError
     */
    public function testLogError(): void
    {
        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects(self::once())
            ->method('critical')
            ->with(
                self::identicalTo('Test message'),
                self::identicalTo(['error' => $exception]),
            );

        $this->errorHandler->logError($exception);
    }

    /**
     * @covers \Netgen\Layouts\Error\DebugErrorHandler::logError
     * @covers \Netgen\Layouts\Error\DebugErrorHandler::logError
     */
    public function testLogErrorWithCustomMessage(): void
    {
        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects(self::once())
            ->method('critical')
            ->with(
                self::identicalTo('Custom message'),
                self::identicalTo(['error' => $exception]),
            );

        $this->errorHandler->logError($exception, 'Custom message');
    }

    /**
     * @covers \Netgen\Layouts\Error\DebugErrorHandler::logError
     * @covers \Netgen\Layouts\Error\DebugErrorHandler::logError
     */
    public function testLogErrorWithEmptyMessage(): void
    {
        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects(self::once())
            ->method('critical')
            ->with(
                self::identicalTo(''),
                self::identicalTo(['error' => $exception]),
            );

        $this->errorHandler->logError($exception, '');
    }

    /**
     * @covers \Netgen\Layouts\Error\DebugErrorHandler::logError
     * @covers \Netgen\Layouts\Error\DebugErrorHandler::logError
     */
    public function testLogErrorWithContext(): void
    {
        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects(self::once())
            ->method('critical')
            ->with(
                self::identicalTo('Test message'),
                self::identicalTo(['value' => 42, 'error' => $exception]),
            );

        $this->errorHandler->logError($exception, null, ['value' => 42]);
    }
}
