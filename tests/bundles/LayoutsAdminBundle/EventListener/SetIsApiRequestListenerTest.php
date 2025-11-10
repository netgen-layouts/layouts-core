<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\EventListener;

use Netgen\Bundle\LayoutsAdminBundle\EventListener\SetIsApiRequestListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class SetIsApiRequestListenerTest extends TestCase
{
    private SetIsApiRequestListener $listener;

    protected function setUp(): void
    {
        $this->listener = new SetIsApiRequestListener();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\SetIsApiRequestListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [KernelEvents::REQUEST => ['onKernelRequest', 30]],
            $this->listener::getSubscribedEvents(),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\SetIsApiRequestListener::onKernelRequest
     */
    public function testOnKernelRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set('_route', 'nglayouts_app_api_load_block');

        $event = new RequestEvent($kernelMock, $request, HttpKernelInterface::MAIN_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertTrue($event->getRequest()->attributes->get(SetIsApiRequestListener::API_FLAG_NAME));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\SetIsApiRequestListener::onKernelRequest
     */
    public function testOnKernelRequestWithInvalidRoute(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set('_route', 'some_route');

        $event = new RequestEvent($kernelMock, $request, HttpKernelInterface::MAIN_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertFalse($event->getRequest()->attributes->has(SetIsApiRequestListener::API_FLAG_NAME));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\SetIsApiRequestListener::onKernelRequest
     */
    public function testOnKernelRequestInSubRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new RequestEvent($kernelMock, $request, HttpKernelInterface::SUB_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertFalse($event->getRequest()->attributes->has(SetIsApiRequestListener::API_FLAG_NAME));
    }
}
