<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\EventListener\HttpCache;

use Exception;
use Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\LayoutResponseListener;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\HttpCache\TaggerInterface;
use Netgen\Layouts\View\View\LayoutView;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

#[CoversClass(LayoutResponseListener::class)]
final class LayoutResponseListenerTest extends TestCase
{
    private MockObject&TaggerInterface $taggerMock;

    private LayoutResponseListener $listener;

    protected function setUp(): void
    {
        $this->taggerMock = $this->createMock(TaggerInterface::class);

        $this->listener = new LayoutResponseListener($this->taggerMock);
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [
                ResponseEvent::class => ['onKernelResponse', 10],
                ExceptionEvent::class => 'onKernelException',
            ],
            $this->listener::getSubscribedEvents(),
        );
    }

    public function testOnKernelResponse(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $layout = new Layout();

        $request->attributes->set('nglLayoutView', new LayoutView($layout));

        $event = new ResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            new Response(),
        );

        $this->taggerMock
            ->expects($this->once())
            ->method('tagLayout')
            ->with(self::identicalTo($layout));

        $this->listener->onKernelResponse($event);
    }

    public function testOnKernelResponseWithOverriddenLayout(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $layout = new Layout();
        $layout2 = new Layout();

        $request->attributes->set('nglLayoutView', new LayoutView($layout));
        $request->attributes->set('nglOverrideLayoutView', new LayoutView($layout2));

        $event = new ResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            new Response(),
        );

        $this->taggerMock
            ->expects($this->once())
            ->method('tagLayout')
            ->with(self::identicalTo($layout2));

        $this->listener->onKernelResponse($event);
    }

    public function testOnKernelResponseWithSubRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('nglLayoutView', new LayoutView(new Layout()));

        $event = new ResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            new Response(),
        );

        $this->taggerMock
            ->expects($this->never())
            ->method('tagLayout');

        $this->listener->onKernelResponse($event);
    }

    public function testOnKernelResponseWithoutSupportedValue(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('nglLayoutView', 42);

        $event = new ResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            new Response(),
        );

        $this->taggerMock
            ->expects($this->never())
            ->method('tagLayout');

        $this->listener->onKernelResponse($event);
    }

    public function testOnKernelResponseWithException(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $layout = new Layout();
        $request->attributes->set('nglExceptionLayoutView', new LayoutView($layout));

        $event = new ResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            new Response(),
        );

        $this->taggerMock
            ->expects($this->once())
            ->method('tagLayout')
            ->with(self::identicalTo($layout));

        $this->listener->onKernelException(
            new ExceptionEvent(
                $kernelMock,
                $request,
                HttpKernelInterface::MAIN_REQUEST,
                new Exception(),
            ),
        );

        $this->listener->onKernelResponse($event);
    }

    public function testOnKernelResponseWithExceptionAndSubRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('nglExceptionLayoutView', new LayoutView(new Layout()));

        $event = new ResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            new Response(),
        );

        $this->taggerMock
            ->expects($this->never())
            ->method('tagLayout');

        $this->listener->onKernelException(
            new ExceptionEvent(
                $kernelMock,
                $request,
                HttpKernelInterface::SUB_REQUEST,
                new Exception(),
            ),
        );

        $this->listener->onKernelResponse($event);
    }

    public function testOnKernelResponseWithExceptionAndWithoutSupportedValue(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('nglExceptionLayoutView', 42);

        $event = new ResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            new Response(),
        );

        $this->taggerMock
            ->expects($this->never())
            ->method('tagLayout');

        $this->listener->onKernelException(
            new ExceptionEvent(
                $kernelMock,
                $request,
                HttpKernelInterface::MAIN_REQUEST,
                new Exception(),
            ),
        );

        $this->listener->onKernelResponse($event);
    }
}
