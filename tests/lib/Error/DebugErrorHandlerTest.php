<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Error;

use Exception;
use Netgen\Layouts\Error\DebugErrorHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

#[CoversClass(DebugErrorHandler::class)]
final class DebugErrorHandlerTest extends TestCase
{
    private MockObject&LoggerInterface $loggerMock;

    private DebugErrorHandler $errorHandler;

    protected function setUp(): void
    {
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->errorHandler = new DebugErrorHandler($this->loggerMock);
    }

    public function testHandleError(): void
    {
        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                self::identicalTo('Test message'),
                self::identicalTo(['error' => $exception]),
            );

        $this->errorHandler->handleError($exception);
    }

    public function testHandleErrorWithCustomMessage(): void
    {
        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                self::identicalTo('Custom message'),
                self::identicalTo(['error' => $exception]),
            );

        $this->errorHandler->handleError($exception, 'Custom message');
    }

    public function testHandleErrorWithEmptyMessage(): void
    {
        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                self::identicalTo(''),
                self::identicalTo(['error' => $exception]),
            );

        $this->errorHandler->handleError($exception, '');
    }

    public function testHandleErrorWithContext(): void
    {
        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                self::identicalTo('Test message'),
                self::identicalTo(['value' => 42, 'error' => $exception]),
            );

        $this->errorHandler->handleError($exception, null, ['value' => 42]);
    }

    public function testHandleErrorThrowsError(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test message');

        $this->errorHandler = new DebugErrorHandler($this->loggerMock, true);

        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                self::identicalTo('Test message'),
                self::identicalTo(['error' => $exception]),
            );

        $this->errorHandler->handleError($exception);
    }

    public function testHandleErrorThrowsErrorWithCustomMessage(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test message');

        $this->errorHandler = new DebugErrorHandler($this->loggerMock, true);

        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                self::identicalTo('Custom message'),
                self::identicalTo(['error' => $exception]),
            );

        $this->errorHandler->handleError($exception, 'Custom message');
    }

    public function testHandleErrorThrowsErrorWithEmptyMessage(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test message');

        $this->errorHandler = new DebugErrorHandler($this->loggerMock, true);

        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                self::identicalTo(''),
                self::identicalTo(['error' => $exception]),
            );

        $this->errorHandler->handleError($exception, '');
    }

    public function testHandleErrorThrowsErrorWithContext(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test message');

        $this->errorHandler = new DebugErrorHandler($this->loggerMock, true);

        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                self::identicalTo('Test message'),
                self::identicalTo(['value' => 42, 'error' => $exception]),
            );

        $this->errorHandler->handleError($exception, null, ['value' => 42]);
    }

    public function testLogError(): void
    {
        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                self::identicalTo('Test message'),
                self::identicalTo(['error' => $exception]),
            );

        $this->errorHandler->logError($exception);
    }

    public function testLogErrorWithCustomMessage(): void
    {
        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                self::identicalTo('Custom message'),
                self::identicalTo(['error' => $exception]),
            );

        $this->errorHandler->logError($exception, 'Custom message');
    }

    public function testLogErrorWithEmptyMessage(): void
    {
        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                self::identicalTo(''),
                self::identicalTo(['error' => $exception]),
            );

        $this->errorHandler->logError($exception, '');
    }

    public function testLogErrorWithContext(): void
    {
        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                self::identicalTo('Test message'),
                self::identicalTo(['value' => 42, 'error' => $exception]),
            );

        $this->errorHandler->logError($exception, null, ['value' => 42]);
    }
}
