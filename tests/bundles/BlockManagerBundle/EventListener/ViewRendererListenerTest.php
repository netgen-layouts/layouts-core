<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener;

use Exception;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\Stubs\ErrorHandler;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\ViewRendererInterface;
use Netgen\Bundle\BlockManagerBundle\EventListener\ViewRendererListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class ViewRendererListenerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $viewRendererMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\EventListener\ViewRendererListener
     */
    private $listener;

    public function setUp()
    {
        $this->viewRendererMock = $this->createMock(ViewRendererInterface::class);
        $this->listener = new ViewRendererListener($this->viewRendererMock, new ErrorHandler());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ViewRendererListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            [KernelEvents::VIEW => ['onView', -255]],
            $this->listener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ViewRendererListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ViewRendererListener::onView
     */
    public function testOnView()
    {
        $view = new View(['value' => new Value()]);

        $response = new Response();
        $response->headers->set('X-NGBM-Test', 'test');
        $view->setResponse($response);

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderView')
            ->with($this->equalTo($view))
            ->will($this->returnValue('rendered content'));

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseForControllerResultEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $view
        );

        $this->listener->onView($event);

        $this->assertInstanceOf(Response::class, $event->getResponse());

        // Verify that we use the response available in view object
        $this->assertEquals($event->getResponse()->headers->get('X-NGBM-Test'), 'test');

        $this->assertEquals('rendered content', $event->getResponse()->getContent());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ViewRendererListener::onView
     */
    public function testOnViewWithException()
    {
        $view = new View(['value' => new Value()]);

        $response = new Response();
        $response->headers->set('X-NGBM-Test', 'test');
        $view->setResponse($response);

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderView')
            ->with($this->equalTo($view))
            ->will($this->throwException(new Exception()));

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseForControllerResultEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $view
        );

        $this->listener->onView($event);

        $this->assertInstanceOf(Response::class, $event->getResponse());

        // Verify that we use the response available in view object
        $this->assertEquals($event->getResponse()->headers->get('X-NGBM-Test'), 'test');

        $this->assertEquals('', $event->getResponse()->getContent());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ViewRendererListener::onView
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
}
