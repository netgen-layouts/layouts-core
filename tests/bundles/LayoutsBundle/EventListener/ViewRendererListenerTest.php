<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\EventListener;

use Exception;
use Netgen\BlockManager\Tests\API\Stubs\Value;
use Netgen\BlockManager\Tests\Stubs\ErrorHandler;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\ViewRendererInterface;
use Netgen\Bundle\LayoutsBundle\EventListener\ViewRendererListener;
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
     * @var \Netgen\Bundle\LayoutsBundle\EventListener\ViewRendererListener
     */
    private $listener;

    public function setUp(): void
    {
        $this->viewRendererMock = $this->createMock(ViewRendererInterface::class);
        $this->listener = new ViewRendererListener(
            $this->viewRendererMock,
            new ErrorHandler()
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\ViewRendererListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [KernelEvents::VIEW => ['onView', -255]],
            $this->listener::getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\ViewRendererListener::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\ViewRendererListener::onView
     */
    public function testOnView(): void
    {
        $view = new View(new Value());

        $response = new Response();
        $response->headers->set('X-Layouts-Test', 'test');

        $view->setResponse($response);

        $this->viewRendererMock
            ->expects(self::once())
            ->method('renderView')
            ->with(self::identicalTo($view))
            ->willReturn('rendered content');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseForControllerResultEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $view
        );

        $this->listener->onView($event);

        self::assertInstanceOf(Response::class, $event->getResponse());

        // Verify that we use the response available in view object
        self::assertSame($event->getResponse()->headers->get('X-Layouts-Test'), 'test');

        self::assertSame('rendered content', $event->getResponse()->getContent());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\ViewRendererListener::onView
     */
    public function testOnViewWithException(): void
    {
        $view = new View(new Value());

        $response = new Response();
        $response->headers->set('X-Layouts-Test', 'test');

        $view->setResponse($response);

        $this->viewRendererMock
            ->expects(self::once())
            ->method('renderView')
            ->with(self::identicalTo($view))
            ->willThrowException(new Exception());

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseForControllerResultEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $view
        );

        $this->listener->onView($event);

        self::assertInstanceOf(Response::class, $event->getResponse());

        // Verify that we use the response available in view object
        self::assertSame($event->getResponse()->headers->get('X-Layouts-Test'), 'test');

        self::assertSame('', $event->getResponse()->getContent());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\ViewRendererListener::onView
     */
    public function testOnViewWithoutViewResponse(): void
    {
        $view = new View(new Value());

        $this->viewRendererMock
            ->expects(self::never())
            ->method('renderView');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseForControllerResultEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $view
        );

        $this->listener->onView($event);

        self::assertFalse($event->hasResponse());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\ViewRendererListener::onView
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

        self::assertFalse($event->hasResponse());
    }
}
