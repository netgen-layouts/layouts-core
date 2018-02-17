<?php

namespace Netgen\BlockManager\Tests\Error;

use Exception;
use Netgen\BlockManager\Error\DebugErrorHandler;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class DebugErrorHandlerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $loggerMock;

    /**
     * @var \Netgen\BlockManager\Error\DebugErrorHandler
     */
    private $errorHandler;

    public function setUp()
    {
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->errorHandler = new DebugErrorHandler($this->loggerMock, false);
    }

    /**
     * @covers \Netgen\BlockManager\Error\DebugErrorHandler::handleError
     * @covers \Netgen\BlockManager\Error\DebugErrorHandler::logError
     */
    public function testHandleError()
    {
        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                $this->equalTo('Test message'),
                array('error' => $exception)
            );

        $this->errorHandler->handleError($exception);
    }

    /**
     * @covers \Netgen\BlockManager\Error\DebugErrorHandler::handleError
     * @covers \Netgen\BlockManager\Error\DebugErrorHandler::logError
     */
    public function testHandleErrorWithCustomMessage()
    {
        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                $this->equalTo('Custom message'),
                array('error' => $exception)
            );

        $this->errorHandler->handleError($exception, 'Custom message');
    }

    /**
     * @covers \Netgen\BlockManager\Error\DebugErrorHandler::handleError
     * @covers \Netgen\BlockManager\Error\DebugErrorHandler::logError
     */
    public function testHandleErrorWithEmptyMessage()
    {
        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                $this->equalTo(''),
                array('error' => $exception)
            );

        $this->errorHandler->handleError($exception, '');
    }

    /**
     * @covers \Netgen\BlockManager\Error\DebugErrorHandler::handleError
     * @covers \Netgen\BlockManager\Error\DebugErrorHandler::logError
     */
    public function testHandleErrorWithContext()
    {
        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                $this->equalTo('Test message'),
                array('value' => 42, 'error' => $exception)
            );

        $this->errorHandler->handleError($exception, null, array('value' => 42));
    }

    /**
     * @covers \Netgen\BlockManager\Error\DebugErrorHandler::handleError
     * @covers \Netgen\BlockManager\Error\DebugErrorHandler::logError
     * @expectedException \Exception
     * @expectedExceptionMessage Test message
     */
    public function testHandleErrorThrowsError()
    {
        $this->errorHandler = new DebugErrorHandler($this->loggerMock, true);

        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                $this->equalTo('Test message'),
                array('error' => $exception)
            );

        $this->errorHandler->handleError($exception);
    }

    /**
     * @covers \Netgen\BlockManager\Error\DebugErrorHandler::handleError
     * @covers \Netgen\BlockManager\Error\DebugErrorHandler::logError
     * @expectedException \Exception
     * @expectedExceptionMessage Test message
     */
    public function testHandleErrorThrowsErrorWithCustomMessage()
    {
        $this->errorHandler = new DebugErrorHandler($this->loggerMock, true);

        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                $this->equalTo('Custom message'),
                array('error' => $exception)
            );

        $this->errorHandler->handleError($exception, 'Custom message');
    }

    /**
     * @covers \Netgen\BlockManager\Error\DebugErrorHandler::handleError
     * @covers \Netgen\BlockManager\Error\DebugErrorHandler::logError
     * @expectedException \Exception
     * @expectedExceptionMessage Test message
     */
    public function testHandleErrorThrowsErrorWithEmptyMessage()
    {
        $this->errorHandler = new DebugErrorHandler($this->loggerMock, true);

        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                $this->equalTo(''),
                array('error' => $exception)
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
    public function testHandleErrorThrowsErrorWithContext()
    {
        $this->errorHandler = new DebugErrorHandler($this->loggerMock, true);

        $exception = new Exception('Test message');

        $this->loggerMock
            ->expects($this->once())
            ->method('critical')
            ->with(
                $this->equalTo('Test message'),
                array('value' => 42, 'error' => $exception)
            );

        $this->errorHandler->handleError($exception, null, array('value' => 42));
    }
}
