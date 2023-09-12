<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\EventListener;

use Exception;
use Netgen\Bundle\LayoutsAdminBundle\EventListener\ExceptionSerializerListener;
use Netgen\Bundle\LayoutsAdminBundle\EventListener\SetIsApiRequestListener;
use Netgen\Layouts\Tests\Utils\BackwardsCompatibility\CreateEventTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

final class ExceptionSerializerListenerTest extends TestCase
{
    use CreateEventTrait;

    private MockObject $serializerMock;

    private MockObject $loggerMock;

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

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\ExceptionSerializerListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [KernelEvents::EXCEPTION => ['onException', 5]],
            $this->listener::getSubscribedEvents(),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\ExceptionSerializerListener::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\ExceptionSerializerListener::onException
     */
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

        $event = $this->createExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
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

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\ExceptionSerializerListener::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\ExceptionSerializerListener::onException
     *
     * @dataProvider onExceptionWithHttpExceptionDataProvider
     */
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

        $event = $this->createExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
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

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\ExceptionSerializerListener::onException
     */
    public function testOnExceptionWithNoApiRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = $this->createExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Exception(),
        );

        $this->listener->onException($event);

        self::assertFalse($event->hasResponse());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\ExceptionSerializerListener::onException
     */
    public function testOnExceptionInSubRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = $this->createExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            new Exception(),
        );

        $this->listener->onException($event);

        self::assertFalse($event->hasResponse());
    }
}
