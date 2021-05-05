<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\EventListener;

use Netgen\Bundle\LayoutsBundle\EventListener\ViewListener;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Tests\Utils\BackwardsCompatibility\CreateEventTrait;
use Netgen\Layouts\View\View\BlockView;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class ViewListenerTest extends TestCase
{
    use CreateEventTrait;

    private ViewListener $listener;

    protected function setUp(): void
    {
        $this->listener = new ViewListener();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\ViewListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [KernelEvents::VIEW => 'onView'],
            $this->listener::getSubscribedEvents(),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\ViewListener::onView
     */
    public function testOnView(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $blockView = new BlockView(new Block());

        $event = $this->createViewEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $blockView,
        );

        $this->listener->onView($event);

        self::assertTrue($request->attributes->has('nglView'));
        self::assertSame($blockView, $request->attributes->get('nglView'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\ViewListener::onView
     */
    public function testOnViewWithSubRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $blockView = new BlockView(new Block());

        $event = $this->createViewEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            $blockView,
        );

        $this->listener->onView($event);

        self::assertFalse($request->attributes->has('nglView'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\ViewListener::onView
     */
    public function testOnViewWithoutSupportedValue(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = $this->createViewEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            42,
        );

        $this->listener->onView($event);

        self::assertFalse($request->attributes->has('nglView'));
    }
}
