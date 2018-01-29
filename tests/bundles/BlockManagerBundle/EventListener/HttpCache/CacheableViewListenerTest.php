<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener\HttpCache;

use Netgen\BlockManager\View\View\BlockView;
use Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class CacheableViewListenerTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener
     */
    private $listener;

    public function setUp()
    {
        $this->listener = new CacheableViewListener();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            array(
                KernelEvents::VIEW => 'onView',
                KernelEvents::RESPONSE => array('onKernelResponse', -255),
            ),
            $this->listener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::onView
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::setUpCachingHeaders
     */
    public function testOnView()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $blockView = new BlockView();
        $blockView->setSharedMaxAge(42);

        $event = new GetResponseForControllerResultEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $blockView
        );

        $this->listener->onView($event);

        $this->assertTrue($request->attributes->has('ngbmView'));
        $this->assertEquals($blockView, $request->attributes->get('ngbmView'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::onView
     */
    public function testOnViewWithSubRequest()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $blockView = new BlockView();
        $blockView->setSharedMaxAge(42);

        $event = new GetResponseForControllerResultEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            $blockView
        );

        $this->listener->onView($event);

        $this->assertNull($blockView->getResponse()->getMaxAge());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::onView
     */
    public function testOnViewWithoutSupportedValue()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseForControllerResultEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            42
        );

        $this->listener->onView($event);

        $this->assertNull($event->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::onKernelResponse
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::setUpCachingHeaders
     */
    public function testOnKernelResponse()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $blockView = new BlockView();
        $blockView->setSharedMaxAge(42);

        $request->attributes->set('ngbmView', $blockView);

        $event = new FilterResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Response()
        );

        $this->listener->onKernelResponse($event);

        $this->assertEquals(42, $event->getResponse()->getMaxAge());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::onKernelResponse
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::setUpCachingHeaders
     */
    public function testOnKernelResponseWithDisabledCache()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $blockView = new BlockView();
        $blockView->setIsCacheable(false);
        $blockView->setSharedMaxAge(42);

        $request->attributes->set('blockView', $blockView);

        $event = new FilterResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Response()
        );

        $this->listener->onKernelResponse($event);

        $this->assertNull($event->getResponse()->getMaxAge());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::onKernelResponse
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::setUpCachingHeaders
     */
    public function testOnKernelResponseWithExistingHeaders()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $blockView = new BlockView();
        $blockView->setSharedMaxAge(42);

        $request->attributes->set('blockView', $blockView);

        $response = new Response();
        $response->setSharedMaxAge(41);

        $event = new FilterResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $response
        );

        $this->listener->onKernelResponse($event);

        $this->assertEquals(41, $event->getResponse()->getMaxAge());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::onKernelResponse
     */
    public function testOnKernelResponseWithSubRequest()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $blockView = new BlockView();
        $blockView->setSharedMaxAge(42);

        $request->attributes->set('blockView', $blockView);

        $event = new FilterResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            new Response()
        );

        $this->listener->onKernelResponse($event);

        $this->assertNull($event->getResponse()->getMaxAge());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::onKernelResponse
     */
    public function testOnKernelResponseWithoutSupportedValue()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new FilterResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Response()
        );

        $this->listener->onKernelResponse($event);

        $this->assertNull($event->getResponse()->getMaxAge());
    }
}
