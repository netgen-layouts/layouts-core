<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\EventListener;

use Netgen\Bundle\LayoutsAdminBundle\EventListener\RequestBodyListener;
use Netgen\Bundle\LayoutsAdminBundle\EventListener\SetIsApiRequestListener;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

#[CoversClass(RequestBodyListener::class)]
final class RequestBodyListenerTest extends TestCase
{
    private RequestBodyListener $listener;

    protected function setUp(): void
    {
        $this->listener = new RequestBodyListener();
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [RequestEvent::class => 'onKernelRequest'],
            $this->listener::getSubscribedEvents(),
        );
    }

    public function testOnKernelRequest(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/', Request::METHOD_POST, [], [], [], [], '{"test": "value"}');
        $request->headers->set('Content-Type', 'application/json');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new RequestEvent($kernelStub, $request, HttpKernelInterface::MAIN_REQUEST);
        $this->listener->onKernelRequest($event);

        $request = $event->getRequest();

        self::assertTrue($request->attributes->has('data'));

        $data = $event->getRequest()->attributes->get('data');
        self::assertInstanceOf(InputBag::class, $data);

        self::assertSame('value', $data->get('test'));
    }

    public function testOnKernelRequestWithNonApiRoute(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/', Request::METHOD_POST, [], [], [], [], '{"test": "value"}');
        $request->headers->set('Content-Type', 'application/json');

        $event = new RequestEvent($kernelStub, $request, HttpKernelInterface::MAIN_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertFalse($event->getRequest()->attributes->has('data'));
    }

    public function testOnKernelRequestInSubRequest(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/', Request::METHOD_POST, [], [], [], [], '{"test": "value"}');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $request->headers->set('Content-Type', 'application/json');

        $event = new RequestEvent($kernelStub, $request, HttpKernelInterface::SUB_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertFalse($event->getRequest()->attributes->has('data'));
    }

    public function testOnKernelRequestWithInvalidMethod(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new RequestEvent($kernelStub, $request, HttpKernelInterface::MAIN_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertFalse($event->getRequest()->attributes->has('data'));
    }

    public function testOnKernelRequestWithInvalidContentType(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/', Request::METHOD_POST, [], [], [], [], '{"test": "value"}');
        $request->headers->set('Content-Type', 'some/type');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new RequestEvent($kernelStub, $request, HttpKernelInterface::MAIN_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertFalse($event->getRequest()->attributes->has('data'));
    }

    public function testOnKernelRequestWithInvalidJson(): void
    {
        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Request body has an invalid format');

        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/', Request::METHOD_POST, [], [], [], [], '{]');
        $request->headers->set('Content-Type', 'application/json');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new RequestEvent($kernelStub, $request, HttpKernelInterface::MAIN_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    public function testOnKernelRequestWithNonArrayJson(): void
    {
        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Request body has an invalid format');

        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/', Request::METHOD_POST, [], [], [], [], '42');
        $request->headers->set('Content-Type', 'application/json');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new RequestEvent($kernelStub, $request, HttpKernelInterface::MAIN_REQUEST);
        $this->listener->onKernelRequest($event);
    }
}
