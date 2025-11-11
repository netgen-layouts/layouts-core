<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\EventListener;

use Netgen\Bundle\LayoutsBundle\EventListener\ViewListener;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\View\View\BlockView;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

#[CoversClass(ViewListener::class)]
final class ViewListenerTest extends TestCase
{
    private ViewListener $listener;

    protected function setUp(): void
    {
        $this->listener = new ViewListener();
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [KernelEvents::VIEW => 'onView'],
            $this->listener::getSubscribedEvents(),
        );
    }

    public function testOnView(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $blockView = new BlockView(new Block());

        $event = new ViewEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $blockView,
        );

        $this->listener->onView($event);

        self::assertTrue($request->attributes->has('nglView'));
        self::assertSame($blockView, $request->attributes->get('nglView'));
    }

    public function testOnViewWithSubRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $blockView = new BlockView(new Block());

        $event = new ViewEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            $blockView,
        );

        $this->listener->onView($event);

        self::assertFalse($request->attributes->has('nglView'));
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

        self::assertFalse($request->attributes->has('nglView'));
    }
}
