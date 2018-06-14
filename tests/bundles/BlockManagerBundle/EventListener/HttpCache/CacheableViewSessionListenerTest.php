<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener\HttpCache;

use Netgen\BlockManager\View\View\BlockView;
use Netgen\BlockManager\View\View\LayoutView;
use Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewSessionListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\EventListener\SessionListener;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;

final class CacheableViewSessionListenerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $innerListenerMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewSessionListener
     */
    private $listener;

    public function setUp(): void
    {
        if (Kernel::VERSION_ID < 30400) {
            $this->markTestSkipped('CacheableViewSessionListener does nothing on versions of Symfony lower than 3.4');
        }

        $this->innerListenerMock = $this->createMock(SessionListener::class);

        $this->listener = new CacheableViewSessionListener($this->innerListenerMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewSessionListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewSessionListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        $listener = $this->listener;

        $this->assertInternalType('array', $listener::getSubscribedEvents());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewSessionListener::onKernelRequest
     */
    public function testOnKernelRequest(): void
    {
        $event = new GetResponseEvent(
            $this->createMock(KernelInterface::class),
            Request::create('/'),
            KernelInterface::MASTER_REQUEST
        );

        $this->innerListenerMock
            ->expects($this->once())
            ->method('onKernelRequest')
            ->with($this->equalTo($event));

        $this->listener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewSessionListener::onKernelResponse
     */
    public function testOnKernelResponseWithNoView(): void
    {
        $event = new FilterResponseEvent(
            $this->createMock(KernelInterface::class),
            Request::create('/'),
            KernelInterface::MASTER_REQUEST,
            new Response()
        );

        $this->innerListenerMock
            ->expects($this->once())
            ->method('onKernelResponse')
            ->with($this->equalTo($event));

        $this->listener->onKernelResponse($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewSessionListener::onKernelResponse
     */
    public function testOnKernelResponseInSubRequest(): void
    {
        $event = new FilterResponseEvent(
            $this->createMock(KernelInterface::class),
            Request::create('/'),
            KernelInterface::SUB_REQUEST,
            new Response()
        );

        $this->innerListenerMock
            ->expects($this->never())
            ->method('onKernelResponse');

        $this->listener->onKernelResponse($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewSessionListener::onKernelResponse
     */
    public function testOnKernelResponseWithCacheableBlockView(): void
    {
        $request = Request::create('/');
        $request->attributes->set('ngbmView', new BlockView());

        $event = new FilterResponseEvent(
            $this->createMock(KernelInterface::class),
            $request,
            KernelInterface::MASTER_REQUEST,
            new Response()
        );

        $this->innerListenerMock
            ->expects($this->never())
            ->method('onKernelResponse');

        $this->listener->onKernelResponse($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewSessionListener::onKernelResponse
     */
    public function testOnKernelResponseWithNonCacheableBlockView(): void
    {
        $request = Request::create('/');
        $blockView = new BlockView();
        $blockView->setIsCacheable(false);
        $request->attributes->set('ngbmView', $blockView);

        $event = new FilterResponseEvent(
            $this->createMock(KernelInterface::class),
            $request,
            KernelInterface::MASTER_REQUEST,
            new Response()
        );

        $this->innerListenerMock
            ->expects($this->once())
            ->method('onKernelResponse')
            ->with($this->equalTo($event));

        $this->listener->onKernelResponse($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewSessionListener::onKernelResponse
     */
    public function testOnKernelResponseWithNonCacheableView(): void
    {
        $request = Request::create('/');
        $request->attributes->set('ngbmView', new LayoutView());

        $event = new FilterResponseEvent(
            $this->createMock(KernelInterface::class),
            $request,
            KernelInterface::MASTER_REQUEST,
            new Response()
        );

        $this->innerListenerMock
            ->expects($this->once())
            ->method('onKernelResponse')
            ->with($this->equalTo($event));

        $this->listener->onKernelResponse($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewSessionListener::onKernelResponse
     */
    public function testOnKernelResponseWithInvalidView(): void
    {
        $request = Request::create('/');
        $request->attributes->set('ngbmView', 42);

        $event = new FilterResponseEvent(
            $this->createMock(KernelInterface::class),
            $request,
            KernelInterface::MASTER_REQUEST,
            new Response()
        );

        $this->innerListenerMock
            ->expects($this->once())
            ->method('onKernelResponse')
            ->with($this->equalTo($event));

        $this->listener->onKernelResponse($event);
    }
}
