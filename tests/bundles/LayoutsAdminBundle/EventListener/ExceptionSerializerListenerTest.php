<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\EventListener;

use Exception;
use Netgen\Bundle\LayoutsAdminBundle\EventListener\ExceptionSerializerListener;
use Netgen\Bundle\LayoutsAdminBundle\EventListener\SetIsApiRequestListener;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[CoversClass(ExceptionSerializerListener::class)]
final class ExceptionSerializerListenerTest extends TestCase
{
    private MockObject&SerializerInterface $serializerMock;

    private MockObject&LoggerInterface $loggerMock;

    private ExceptionSerializerListener $listener;

    protected function setUp(): void
    {
        $this->serializerMock = $this->createMock(SerializerInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->listener = new ExceptionSerializerListener(
            $this->serializerMock,
            $this->loggerMock,
        );
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [ExceptionEvent::class => ['onException', 5]],
            $this->listener::getSubscribedEvents(),
        );
    }

    public function testOnException(): void
    {
        $exception = new Exception();

        $this->serializerMock
            ->expects(self::once())
            ->method('serialize')
            ->with(
                self::identicalTo($exception),
                self::identicalTo('json'),
            )
            ->willReturn('serialized content');

        $this->loggerMock
            ->method('critical');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new ExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception,
        );

        $this->listener->onException($event);

        self::assertInstanceOf(
            JsonResponse::class,
            $event->getResponse(),
        );

        self::assertSame(
            'serialized content',
            $event->getResponse()->getContent(),
        );
    }

    #[DataProvider('onExceptionWithHttpExceptionDataProvider')]
    public function testOnExceptionWithHttpException(int $statusCode, bool $loggerCalled): void
    {
        $exception = new HttpException($statusCode);

        $this->serializerMock
            ->expects(self::once())
            ->method('serialize')
            ->with(
                self::identicalTo($exception),
                self::identicalTo('json'),
            )
            ->willReturn('serialized content');

        $this->loggerMock
            ->expects($loggerCalled ? self::once() : self::never())
            ->method('critical');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new ExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception,
        );

        $this->listener->onException($event);

        self::assertInstanceOf(
            JsonResponse::class,
            $event->getResponse(),
        );

        self::assertSame(
            'serialized content',
            $event->getResponse()->getContent(),
        );
    }

    public static function onExceptionWithHttpExceptionDataProvider(): iterable
    {
        return [
            [450, false],
            [499, false],
            [500, true],
            [501, true],
            [550, true],
        ];
    }

    public function testOnExceptionWithNoApiRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new ExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            new Exception(),
        );

        $this->listener->onException($event);

        self::assertFalse($event->hasResponse());
    }

    public function testOnExceptionInSubRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new ExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            new Exception(),
        );

        $this->listener->onException($event);

        self::assertFalse($event->hasResponse());
    }
}
