<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\EventListener;

use Netgen\Bundle\LayoutsAdminBundle\EventListener\SetIsAdminRequestListener;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

#[CoversClass(SetIsAdminRequestListener::class)]
final class SetIsAdminRequestListenerTest extends TestCase
{
    private SetIsAdminRequestListener $listener;

    protected function setUp(): void
    {
        $this->listener = new SetIsAdminRequestListener(
            $this->createMock(EventDispatcherInterface::class),
        );
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [RequestEvent::class => ['onKernelRequest', 30]],
            $this->listener::getSubscribedEvents(),
        );
    }

    public function testOnKernelRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set('_route', 'nglayouts_admin_layout_resolver_index');

        $event = new RequestEvent($kernelMock, $request, HttpKernelInterface::MAIN_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertTrue(
            $event->getRequest()->attributes->get(SetIsAdminRequestListener::ADMIN_FLAG_NAME),
        );
    }

    public function testOnKernelRequestWithInvalidRoute(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set('_route', 'some_route');

        $event = new RequestEvent($kernelMock, $request, HttpKernelInterface::MAIN_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertFalse($event->getRequest()->attributes->has(SetIsAdminRequestListener::ADMIN_FLAG_NAME));
    }

    public function testOnKernelRequestInSubRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new RequestEvent($kernelMock, $request, HttpKernelInterface::SUB_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertFalse($event->getRequest()->attributes->has(SetIsAdminRequestListener::ADMIN_FLAG_NAME));
    }
}
