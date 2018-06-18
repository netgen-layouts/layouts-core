<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener;

use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\Bundle\BlockManagerBundle\EventListener\SerializerListener;
use Netgen\Bundle\BlockManagerBundle\EventListener\SetIsApiRequestListener;
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
     * @var \Netgen\Bundle\BlockManagerBundle\EventListener\SerializerListener
     */
    private $listener;

    public function setUp(): void
    {
        $this->serializerMock = $this->createMock(SerializerInterface::class);

        $this->listener = new SerializerListener($this->serializerMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\SerializerListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        $this->assertSame(
            [KernelEvents::VIEW => 'onView'],
            $this->listener::getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\SerializerListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\SerializerListener::onView
     */
    public function testOnView(): void
    {
        $value = new VersionedValue(new Value(), 42);

        $this->serializerMock
            ->expects($this->once())
            ->method('serialize')
            ->with(
                $this->equalTo($value),
                $this->equalTo('json'),
                $this->equalTo([])
            )
            ->will(
                $this->returnValue('serialized content')
            );

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

        $this->assertInstanceOf(
            JsonResponse::class,
            $event->getResponse()
        );

        $this->assertSame(
            'serialized content',
            $event->getResponse()->getContent()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\SerializerListener::onView
     */
    public function testOnViewWithNoHtmlRendering(): void
    {
        $value = new VersionedValue(new Value(), 42);

        $this->serializerMock
            ->expects($this->once())
            ->method('serialize')
            ->with(
                $this->equalTo($value),
                $this->equalTo('json'),
                $this->equalTo(['disable_html' => true])
            )
            ->will(
                $this->returnValue('serialized content')
            );

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

        $this->assertInstanceOf(
            JsonResponse::class,
            $event->getResponse()
        );

        $this->assertSame(
            'serialized content',
            $event->getResponse()->getContent()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\SerializerListener::onView
     */
    public function testOnViewInSubRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseForControllerResultEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            new VersionedValue(new Value(), 42)
        );

        $this->listener->onView($event);

        $this->assertFalse($event->hasResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\SerializerListener::onView
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

        $this->assertFalse($event->hasResponse());
    }
}
