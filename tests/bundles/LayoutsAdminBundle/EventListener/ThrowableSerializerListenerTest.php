<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\EventListener;

use Exception;
use Netgen\Bundle\LayoutsAdminBundle\EventListener\SetIsAppRequestListener;
use Netgen\Bundle\LayoutsAdminBundle\EventListener\ThrowableSerializerListener;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[CoversClass(ThrowableSerializerListener::class)]
final class ThrowableSerializerListenerTest extends TestCase
{
    private Stub&SerializerInterface $serializerStub;

    private ThrowableSerializerListener $listener;

    protected function setUp(): void
    {
        $this->serializerStub = self::createStub(SerializerInterface::class);

        $this->listener = new ThrowableSerializerListener(
            $this->serializerStub,
            self::createStub(LoggerInterface::class),
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
        $throwable = new Exception();

        $this->serializerStub
            ->method('serialize')
            ->with(
                self::equalTo(new Value($throwable)),
                self::identicalTo('json'),
            )
            ->willReturn('serialized content');

        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock
            ->expects($this->once())
            ->method('critical');

        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsAppRequestListener::APP_FLAG_NAME, true);

        $event = new ExceptionEvent(
            $kernelStub,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $throwable,
        );

        $listener = new ThrowableSerializerListener(
            $this->serializerStub,
            $loggerMock,
        );

        $listener->onException($event);

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
        $throwable = new HttpException($statusCode);

        $this->serializerStub
            ->method('serialize')
            ->with(
                self::equalTo(new Value($throwable)),
                self::identicalTo('json'),
            )
            ->willReturn('serialized content');

        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock
            ->expects($loggerCalled ? $this->once() : $this->never())
            ->method('critical');

        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsAppRequestListener::APP_FLAG_NAME, true);

        $event = new ExceptionEvent(
            $kernelStub,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $throwable,
        );

        $listener = new ThrowableSerializerListener(
            $this->serializerStub,
            $loggerMock,
        );

        $listener->onException($event);

        self::assertInstanceOf(
            JsonResponse::class,
            $event->getResponse(),
        );

        self::assertSame(
            'serialized content',
            $event->getResponse()->getContent(),
        );
    }

    /**
     * @return iterable<mixed>
     */
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
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new ExceptionEvent(
            $kernelStub,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            new Exception(),
        );

        $this->listener->onException($event);

        self::assertFalse($event->hasResponse());
    }

    public function testOnExceptionInSubRequest(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new ExceptionEvent(
            $kernelStub,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            new Exception(),
        );

        $this->listener->onException($event);

        self::assertFalse($event->hasResponse());
    }
}
