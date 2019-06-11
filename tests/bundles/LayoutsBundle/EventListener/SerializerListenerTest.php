<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\EventListener;

use Netgen\Bundle\LayoutsBundle\EventListener\SerializerListener;
use Netgen\Bundle\LayoutsBundle\EventListener\SetIsApiRequestListener;
use Netgen\Layouts\Serializer\Values\Value;
use Netgen\Layouts\Tests\API\Stubs\Value as APIValue;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

final class SerializerListenerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $serializerMock;

    /**
     * @var \Netgen\Bundle\LayoutsBundle\EventListener\SerializerListener
     */
    private $listener;

    protected function setUp(): void
    {
        $this->serializerMock = $this->createMock(SerializerInterface::class);

        $this->listener = new SerializerListener($this->serializerMock);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\SerializerListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [KernelEvents::VIEW => 'onView'],
            $this->listener::getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\SerializerListener::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\SerializerListener::onView
     */
    public function testOnView(): void
    {
        $value = new Value(new APIValue());

        $this->serializerMock
            ->expects(self::once())
            ->method('serialize')
            ->with(
                self::identicalTo($value),
                self::identicalTo('json'),
                self::identicalTo([])
            )
            ->willReturn('serialized content');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseForControllerResultEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $value
        );

        $this->listener->onView($event);

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
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\SerializerListener::onView
     */
    public function testOnViewWithNoHtmlRendering(): void
    {
        $value = new Value(new APIValue());

        $this->serializerMock
            ->expects(self::once())
            ->method('serialize')
            ->with(
                self::identicalTo($value),
                self::identicalTo('json'),
                self::identicalTo(['disable_html' => true])
            )
            ->willReturn('serialized content');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->query->set('html', 'false');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseForControllerResultEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $value
        );

        $this->listener->onView($event);

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
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\SerializerListener::onView
     */
    public function testOnViewInSubRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseForControllerResultEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            new Value(new APIValue())
        );

        $this->listener->onView($event);

        self::assertFalse($event->hasResponse());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\SerializerListener::onView
     */
    public function testOnViewWithoutSupportedValue(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseForControllerResultEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            42
        );

        $this->listener->onView($event);

        self::assertFalse($event->hasResponse());
    }
}
