<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\EventListener\HttpCache;

use Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\BlockResponseListener;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\HttpCache\TaggerInterface;
use Netgen\Layouts\View\View\BlockView;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class BlockResponseListenerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $taggerMock;

    /**
     * @var \Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\BlockResponseListener
     */
    private $listener;

    public function setUp(): void
    {
        $this->taggerMock = $this->createMock(TaggerInterface::class);

        $this->listener = new BlockResponseListener($this->taggerMock);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\BlockResponseListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [KernelEvents::RESPONSE => ['onKernelResponse', -255]],
            $this->listener::getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\BlockResponseListener::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\BlockResponseListener::onKernelResponse
     */
    public function testOnKernelResponse(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $block = new Block();
        $request->attributes->set('nglView', new BlockView($block));

        $response = new Response();
        $event = new FilterResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $response
        );

        $this->taggerMock
            ->expects(self::once())
            ->method('tagBlock')
            ->with(self::identicalTo($response), self::identicalTo($block));

        $this->listener->onKernelResponse($event);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\BlockResponseListener::onKernelResponse
     */
    public function testOnKernelResponseWithSubRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('nglView', new BlockView(new Block()));

        $event = new FilterResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            new Response()
        );

        $this->taggerMock
            ->expects(self::never())
            ->method('tagBlock');

        $this->listener->onKernelResponse($event);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\BlockResponseListener::onKernelResponse
     */
    public function testOnKernelResponseWithoutSupportedValue(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('nglView', 42);

        $event = new FilterResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Response()
        );

        $this->taggerMock
            ->expects(self::never())
            ->method('tagBlock');

        $this->listener->onKernelResponse($event);
    }
}
