<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener;

use Netgen\BlockManager\View\View\BlockView;
use Netgen\Bundle\BlockManagerBundle\EventListener\ViewListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class ViewListenerTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\EventListener\ViewListener
     */
    private $listener;

    public function setUp(): void
    {
        $this->listener = new ViewListener();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ViewListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        $this->assertEquals(
            [KernelEvents::VIEW => 'onView'],
            $this->listener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ViewListener::onView
     */
    public function testOnView(): void
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
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ViewListener::onView
     */
    public function testOnViewWithSubRequest(): void
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

        $this->assertFalse($request->attributes->has('ngbmView'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ViewListener::onView
     */
    public function testOnViewWithoutSupportedValue(): void
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

        $this->assertFalse($request->attributes->has('ngbmView'));
    }
}
