<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener\HttpCache;

use Exception;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\HttpCache\TaggerInterface;
use Netgen\BlockManager\View\View\LayoutView;
use Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\LayoutResponseListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class LayoutResponseListenerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $taggerMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\LayoutResponseListener
     */
    private $listener;

    public function setUp(): void
    {
        $this->taggerMock = $this->createMock(TaggerInterface::class);

        $this->listener = new LayoutResponseListener($this->taggerMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\LayoutResponseListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        $this->assertEquals(
            [
                KernelEvents::RESPONSE => 'onKernelResponse',
                KernelEvents::EXCEPTION => 'onKernelException',
            ],
            $this->listener::getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\LayoutResponseListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\LayoutResponseListener::onKernelResponse
     */
    public function testOnKernelResponse(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('ngbmLayoutView', new LayoutView(['layout' => new Layout()]));

        $event = new FilterResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Response()
        );

        $this->taggerMock
            ->expects($this->once())
            ->method('tagLayout')
            ->with($this->equalTo(new Response()), $this->equalTo(new Layout()));

        $this->listener->onKernelResponse($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\LayoutResponseListener::onKernelResponse
     */
    public function testOnKernelResponseWithSubRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('ngbmLayoutView', new LayoutView(['layout' => new Layout()]));

        $event = new FilterResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            new Response()
        );

        $this->taggerMock
            ->expects($this->never())
            ->method('tagLayout');

        $this->listener->onKernelResponse($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\LayoutResponseListener::onKernelResponse
     */
    public function testOnKernelResponseWithoutSupportedValue(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('ngbmLayoutView', 42);

        $event = new FilterResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Response()
        );

        $this->taggerMock
            ->expects($this->never())
            ->method('tagLayout');

        $this->listener->onKernelResponse($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\LayoutResponseListener::onKernelException
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\LayoutResponseListener::onKernelResponse
     */
    public function testOnKernelResponseWithException(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('ngbmExceptionLayoutView', new LayoutView(['layout' => new Layout()]));

        $event = new FilterResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Response()
        );

        $this->taggerMock
            ->expects($this->once())
            ->method('tagLayout')
            ->with($this->equalTo(new Response()), $this->equalTo(new Layout()));

        $this->listener->onKernelException(
            new GetResponseForExceptionEvent(
                $kernelMock,
                $request,
                HttpKernelInterface::MASTER_REQUEST,
                new Exception()
            )
        );

        $this->listener->onKernelResponse($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\LayoutResponseListener::onKernelException
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\LayoutResponseListener::onKernelResponse
     */
    public function testOnKernelResponseWithExceptionAndSubRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('ngbmExceptionLayoutView', new LayoutView(['layout' => new Layout()]));

        $event = new FilterResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            new Response()
        );

        $this->taggerMock
            ->expects($this->never())
            ->method('tagLayout');

        $this->listener->onKernelException(
            new GetResponseForExceptionEvent(
                $kernelMock,
                $request,
                HttpKernelInterface::SUB_REQUEST,
                new Exception()
            )
        );

        $this->listener->onKernelResponse($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\LayoutResponseListener::onKernelException
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\LayoutResponseListener::onKernelResponse
     */
    public function testOnKernelResponseWithExceptionAndWithoutSupportedValue(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('ngbmExceptionLayoutView', 42);

        $event = new FilterResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Response()
        );

        $this->taggerMock
            ->expects($this->never())
            ->method('tagLayout');

        $this->listener->onKernelException(
            new GetResponseForExceptionEvent(
                $kernelMock,
                $request,
                HttpKernelInterface::MASTER_REQUEST,
                new Exception()
            )
        );

        $this->listener->onKernelResponse($event);
    }
}
