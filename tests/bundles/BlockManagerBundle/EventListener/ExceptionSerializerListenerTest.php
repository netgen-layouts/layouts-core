<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener;

use Exception;
use Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionSerializerListener;
use Netgen\Bundle\BlockManagerBundle\EventListener\SetIsApiRequestListener;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

final class ExceptionSerializerListenerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $serializerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $loggerMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionSerializerListener
     */
    private $listener;

    public function setUp(): void
    {
        $this->serializerMock = $this->createMock(SerializerInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->listener = new ExceptionSerializerListener(
            $this->serializerMock,
            $this->loggerMock
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionSerializerListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [KernelEvents::EXCEPTION => ['onException', 5]],
            $this->listener::getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionSerializerListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionSerializerListener::onException
     */
    public function testOnException(): void
    {
        $exception = new Exception();

        $this->serializerMock
            ->expects(self::once())
            ->method('serialize')
            ->with(
                self::identicalTo($exception),
                self::identicalTo('json')
            )
            ->willReturn('serialized content');

        $this->loggerMock
            ->expects(self::at(0))
            ->method('critical');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $this->listener->onException($event);

        self::assertInstanceOf(
            JsonResponse::class,
            $event->getResponse()
        );

        self::assertSame(
            'serialized content',
            $event->getResponse()->getContent()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionSerializerListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionSerializerListener::onException
     * @dataProvider onExceptionWithHttpExceptionProvider
     */
    public function testOnExceptionWithHttpException(int $statusCode, bool $loggerCalled): void
    {
        $exception = new HttpException($statusCode);

        $this->serializerMock
            ->expects(self::once())
            ->method('serialize')
            ->with(
                self::identicalTo($exception),
                self::identicalTo('json')
            )
            ->willReturn('serialized content');

        $this->loggerMock
            ->expects($loggerCalled ? self::at(0) : self::never())
            ->method('critical');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $this->listener->onException($event);

        self::assertInstanceOf(
            JsonResponse::class,
            $event->getResponse()
        );

        self::assertSame(
            'serialized content',
            $event->getResponse()->getContent()
        );
    }

    public function onExceptionWithHttpExceptionProvider(): array
    {
        return [
            [450, false],
            [499, false],
            [500, true],
            [501, true],
            [550, true],
        ];
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionSerializerListener::onException
     */
    public function testOnExceptionWithNoApiRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Exception()
        );

        $this->listener->onException($event);

        self::assertFalse($event->hasResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionSerializerListener::onException
     */
    public function testOnExceptionInSubRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            new Exception()
        );

        $this->listener->onException($event);

        self::assertFalse($event->hasResponse());
    }
}
