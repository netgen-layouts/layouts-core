<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\EventListener\HttpCache;

use Exception;
use Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\LayoutResponseListener;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\HttpCache\TaggerInterface;
use Netgen\Layouts\View\View\LayoutView;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

#[CoversClass(LayoutResponseListener::class)]
final class LayoutResponseListenerTest extends TestCase
{
    private Stub&TaggerInterface $taggerStub;

    private LayoutResponseListener $listener;

    protected function setUp(): void
    {
        $this->taggerStub = self::createStub(TaggerInterface::class);

        $this->listener = new LayoutResponseListener($this->taggerStub);
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
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');

        $layout = new Layout();

        $request->attributes->set('nglLayoutView', new LayoutView($layout));

        $event = new ResponseEvent(
            $kernelStub,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            new Response(),
        );

        $this->taggerStub
            ->method('tagLayout')
            ->with(self::identicalTo($layout));

        $this->listener->onKernelResponse($event);
    }

    public function testOnKernelResponseWithOverriddenLayout(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');

        $layout = new Layout();
        $layout2 = new Layout();

        $request->attributes->set('nglLayoutView', new LayoutView($layout));
        $request->attributes->set('nglOverrideLayoutView', new LayoutView($layout2));

        $event = new ResponseEvent(
            $kernelStub,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            new Response(),
        );

        $this->taggerStub
            ->method('tagLayout')
            ->with(self::identicalTo($layout2));

        $this->listener->onKernelResponse($event);
    }

    public function testOnKernelResponseWithSubRequest(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('nglLayoutView', new LayoutView(new Layout()));

        $event = new ResponseEvent(
            $kernelStub,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            new Response(),
        );

        $taggerMock = $this->createMock(TaggerInterface::class);
        $taggerMock
            ->expects($this->never())
            ->method('tagLayout');

        $this->listener = new LayoutResponseListener($taggerMock);

        $this->listener->onKernelResponse($event);
    }

    public function testOnKernelResponseWithoutSupportedValue(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('nglLayoutView', 42);

        $event = new ResponseEvent(
            $kernelStub,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            new Response(),
        );

        $taggerMock = $this->createMock(TaggerInterface::class);
        $taggerMock
            ->expects($this->never())
            ->method('tagLayout');

        $this->listener = new LayoutResponseListener($taggerMock);

        $this->listener->onKernelResponse($event);
    }

    public function testOnKernelResponseWithException(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');

        $layout = new Layout();
        $request->attributes->set('nglExceptionLayoutView', new LayoutView($layout));

        $event = new ResponseEvent(
            $kernelStub,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            new Response(),
        );

        $this->taggerStub
            ->method('tagLayout')
            ->with(self::identicalTo($layout));

        $this->listener->onKernelException(
            new ExceptionEvent(
                $kernelStub,
                $request,
                HttpKernelInterface::MAIN_REQUEST,
                new Exception(),
            ),
        );

        $this->listener->onKernelResponse($event);
    }

    public function testOnKernelResponseWithExceptionAndSubRequest(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('nglExceptionLayoutView', new LayoutView(new Layout()));

        $event = new ResponseEvent(
            $kernelStub,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            new Response(),
        );

        $this->listener->onKernelException(
            new ExceptionEvent(
                $kernelStub,
                $request,
                HttpKernelInterface::SUB_REQUEST,
                new Exception(),
            ),
        );

        $taggerMock = $this->createMock(TaggerInterface::class);
        $taggerMock
            ->expects($this->never())
            ->method('tagLayout');

        $this->listener = new LayoutResponseListener($taggerMock);

        $this->listener->onKernelResponse($event);
    }

    public function testOnKernelResponseWithExceptionAndWithoutSupportedValue(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('nglExceptionLayoutView', 42);

        $event = new ResponseEvent(
            $kernelStub,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            new Response(),
        );

        $this->listener->onKernelException(
            new ExceptionEvent(
                $kernelStub,
                $request,
                HttpKernelInterface::MAIN_REQUEST,
                new Exception(),
            ),
        );

        $taggerMock = $this->createMock(TaggerInterface::class);
        $taggerMock
            ->expects($this->never())
            ->method('tagLayout');

        $this->listener = new LayoutResponseListener($taggerMock);

        $this->listener->onKernelResponse($event);
    }
}
