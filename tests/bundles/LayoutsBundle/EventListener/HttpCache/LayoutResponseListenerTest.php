<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\EventListener\HttpCache;

use Exception;
use Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\LayoutResponseListener;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\HttpCache\TaggerInterface;
use Netgen\Layouts\Tests\Utils\BackwardsCompatibility\CreateEventTrait;
use Netgen\Layouts\View\View\LayoutView;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class LayoutResponseListenerTest extends TestCase
{
    use CreateEventTrait;

    private MockObject $taggerMock;

    private LayoutResponseListener $listener;

    protected function setUp(): void
    {
        $this->taggerMock = $this->createMock(TaggerInterface::class);

        $this->listener = new LayoutResponseListener($this->taggerMock);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\LayoutResponseListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [
                KernelEvents::RESPONSE => ['onKernelResponse', 10],
                KernelEvents::EXCEPTION => 'onKernelException',
            ],
            $this->listener::getSubscribedEvents(),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\LayoutResponseListener::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\LayoutResponseListener::onKernelResponse
     */
    public function testOnKernelResponse(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $layout = new Layout();

        $request->attributes->set('nglLayoutView', new LayoutView($layout));

        $event = $this->createResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Response(),
        );

        $this->taggerMock
            ->expects(self::once())
            ->method('tagLayout')
            ->with(self::identicalTo($layout));

        $this->listener->onKernelResponse($event);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\LayoutResponseListener::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\LayoutResponseListener::onKernelResponse
     */
    public function testOnKernelResponseWithOverriddenLayout(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $layout = new Layout();
        $layout2 = new Layout();

        $request->attributes->set('nglLayoutView', new LayoutView($layout));
        $request->attributes->set('nglOverrideLayoutView', new LayoutView($layout2));

        $event = $this->createResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Response(),
        );

        $this->taggerMock
            ->expects(self::once())
            ->method('tagLayout')
            ->with(self::identicalTo($layout2));

        $this->listener->onKernelResponse($event);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\LayoutResponseListener::onKernelResponse
     */
    public function testOnKernelResponseWithSubRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('nglLayoutView', new LayoutView(new Layout()));

        $event = $this->createResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            new Response(),
        );

        $this->taggerMock
            ->expects(self::never())
            ->method('tagLayout');

        $this->listener->onKernelResponse($event);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\LayoutResponseListener::onKernelResponse
     */
    public function testOnKernelResponseWithoutSupportedValue(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('nglLayoutView', 42);

        $event = $this->createResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Response(),
        );

        $this->taggerMock
            ->expects(self::never())
            ->method('tagLayout');

        $this->listener->onKernelResponse($event);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\LayoutResponseListener::onKernelException
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\LayoutResponseListener::onKernelResponse
     */
    public function testOnKernelResponseWithException(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $layout = new Layout();
        $request->attributes->set('nglExceptionLayoutView', new LayoutView($layout));

        $event = $this->createResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Response(),
        );

        $this->taggerMock
            ->expects(self::once())
            ->method('tagLayout')
            ->with(self::identicalTo($layout));

        $this->listener->onKernelException(
            $this->createExceptionEvent(
                $kernelMock,
                $request,
                HttpKernelInterface::MASTER_REQUEST,
                new Exception(),
            ),
        );

        $this->listener->onKernelResponse($event);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\LayoutResponseListener::onKernelException
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\LayoutResponseListener::onKernelResponse
     */
    public function testOnKernelResponseWithExceptionAndSubRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('nglExceptionLayoutView', new LayoutView(new Layout()));

        $event = $this->createResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            new Response(),
        );

        $this->taggerMock
            ->expects(self::never())
            ->method('tagLayout');

        $this->listener->onKernelException(
            $this->createExceptionEvent(
                $kernelMock,
                $request,
                HttpKernelInterface::SUB_REQUEST,
                new Exception(),
            ),
        );

        $this->listener->onKernelResponse($event);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\LayoutResponseListener::onKernelException
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\LayoutResponseListener::onKernelResponse
     */
    public function testOnKernelResponseWithExceptionAndWithoutSupportedValue(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('nglExceptionLayoutView', 42);

        $event = $this->createResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Response(),
        );

        $this->taggerMock
            ->expects(self::never())
            ->method('tagLayout');

        $this->listener->onKernelException(
            $this->createExceptionEvent(
                $kernelMock,
                $request,
                HttpKernelInterface::MASTER_REQUEST,
                new Exception(),
            ),
        );

        $this->listener->onKernelResponse($event);
    }
}
