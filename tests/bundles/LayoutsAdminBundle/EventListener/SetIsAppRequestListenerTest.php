<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\EventListener;

use Netgen\Bundle\LayoutsAdminBundle\EventListener\SetIsAppRequestListener;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

#[CoversClass(SetIsAppRequestListener::class)]
final class SetIsAppRequestListenerTest extends TestCase
{
    private SetIsAppRequestListener $listener;

    protected function setUp(): void
    {
        $this->listener = new SetIsAppRequestListener();
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
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set('_route', 'nglayouts_app_api_load_block');

        $event = new RequestEvent($kernelStub, $request, HttpKernelInterface::MAIN_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertTrue($request->attributes->getBoolean(SetIsAppRequestListener::APP_FLAG_NAME));
        self::assertTrue($request->attributes->getBoolean(SetIsAppRequestListener::APP_API_FLAG_NAME));
    }

    public function testOnKernelRequestWithAppRoute(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set('_route', 'nglayouts_app_block_edit');

        $event = new RequestEvent($kernelStub, $request, HttpKernelInterface::MAIN_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertTrue($request->attributes->getBoolean(SetIsAppRequestListener::APP_FLAG_NAME));
        self::assertFalse($request->attributes->getBoolean(SetIsAppRequestListener::APP_API_FLAG_NAME));
    }

    public function testOnKernelRequestWithInvalidRoute(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set('_route', 'some_route');

        $event = new RequestEvent($kernelStub, $request, HttpKernelInterface::MAIN_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertFalse($request->attributes->has(SetIsAppRequestListener::APP_FLAG_NAME));
    }

    public function testOnKernelRequestInSubRequest(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new RequestEvent($kernelStub, $request, HttpKernelInterface::SUB_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertFalse($request->attributes->has(SetIsAppRequestListener::APP_FLAG_NAME));
    }
}
