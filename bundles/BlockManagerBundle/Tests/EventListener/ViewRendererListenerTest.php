<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener;

use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\RendererInterface;
use Netgen\Bundle\BlockManagerBundle\EventListener\ViewRendererListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\TestCase;

class ViewRendererListenerTest extends TestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ViewRendererListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $viewRendererMock = $this->createMock(RendererInterface::class);
        $eventListener = new ViewRendererListener($viewRendererMock);

        self::assertEquals(
            array(KernelEvents::VIEW => 'onView'),
            $eventListener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ViewRendererListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ViewRendererListener::onView
     */
    public function testOnView()
    {
        $value = new View(new Value());

        $viewRendererMock = $this->createMock(RendererInterface::class);
        $viewRendererMock
            ->expects($this->once())
            ->method('renderView')
            ->with($this->equalTo($value))
            ->will($this->returnValue('rendered content'));

        $eventListener = new ViewRendererListener($viewRendererMock);

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseForControllerResultEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $value
        );

        $eventListener->onView($event);

        self::assertInstanceOf(
            Response::class,
            $event->getResponse()
        );

        self::assertEquals(
            'rendered content',
            $event->getResponse()->getContent()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ViewRendererListener::onView
     */
    public function testOnViewWithoutSupportedValue()
    {
        $viewRendererMock = $this->createMock(RendererInterface::class);
        $eventListener = new ViewRendererListener($viewRendererMock);

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseForControllerResultEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            42
        );

        $eventListener->onView($event);

        self::assertNull($event->getResponse());
    }
}
