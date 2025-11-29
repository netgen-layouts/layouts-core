<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\EventListener;

use Exception;
use Netgen\Bundle\LayoutsBundle\EventListener\ViewRendererListener;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\Stubs\ErrorHandler;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\ViewRendererInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

#[CoversClass(ViewRendererListener::class)]
final class ViewRendererListenerTest extends TestCase
{
    private MockObject&ViewRendererInterface $viewRendererMock;

    private ViewRendererListener $listener;

    protected function setUp(): void
    {
        $this->viewRendererMock = $this->createMock(ViewRendererInterface::class);
        $this->listener = new ViewRendererListener(
            $this->viewRendererMock,
            new ErrorHandler(),
        );
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [ViewEvent::class => ['onView', -255]],
            $this->listener::getSubscribedEvents(),
        );
    }

    public function testOnView(): void
    {
        $view = new View(new Value());

        $response = new Response();
        $response->headers->set('X-Layouts-Test', 'test');

        $view->response = $response;

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderView')
            ->with(self::identicalTo($view))
            ->willReturn('rendered content');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new ViewEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $view,
        );

        $this->listener->onView($event);

        self::assertInstanceOf(Response::class, $event->getResponse());

        // Verify that we use the response available in view object
        self::assertSame($event->getResponse()->headers->get('X-Layouts-Test'), 'test');

        self::assertSame('rendered content', $event->getResponse()->getContent());
    }

    public function testOnViewWithException(): void
    {
        $view = new View(new Value());

        $response = new Response();
        $response->headers->set('X-Layouts-Test', 'test');

        $view->response = $response;

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderView')
            ->with(self::identicalTo($view))
            ->willThrowException(new Exception());

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new ViewEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $view,
        );

        $this->listener->onView($event);

        self::assertInstanceOf(Response::class, $event->getResponse());

        // Verify that we use the response available in view object
        self::assertSame($event->getResponse()->headers->get('X-Layouts-Test'), 'test');

        self::assertSame('', $event->getResponse()->getContent());
    }

    public function testOnViewWithoutViewResponse(): void
    {
        $view = new View(new Value());

        $this->viewRendererMock
            ->expects($this->never())
            ->method('renderView');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new ViewEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $view,
        );

        $this->listener->onView($event);

        self::assertFalse($event->hasResponse());
    }

    public function testOnViewWithoutSupportedValue(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new ViewEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            42,
        );

        $this->listener->onView($event);

        self::assertFalse($event->hasResponse());
    }
}
