<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\EventListener;

use Netgen\Bundle\LayoutsAdminBundle\EventListener\RequestBodyListener;
use Netgen\Bundle\LayoutsAdminBundle\EventListener\SetIsApiRequestListener;
use Netgen\Layouts\Tests\Utils\BackwardsCompatibility\CreateEventTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

final class RequestBodyListenerTest extends TestCase
{
    use CreateEventTrait;

    private MockObject $decoderMock;

    private RequestBodyListener $listener;

    protected function setUp(): void
    {
        $this->decoderMock = $this->createMock(DecoderInterface::class);

        $this->listener = new RequestBodyListener($this->decoderMock);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\RequestBodyListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [KernelEvents::REQUEST => 'onKernelRequest'],
            $this->listener::getSubscribedEvents(),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\RequestBodyListener::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\RequestBodyListener::onKernelRequest
     */
    public function testOnKernelRequest(): void
    {
        $this->decoderMock
            ->expects(self::once())
            ->method('decode')
            ->with(self::identicalTo('{"test": "value"}'))
            ->willReturn(['test' => 'value']);

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/', Request::METHOD_POST, [], [], [], [], '{"test": "value"}');
        $request->headers->set('Content-Type', 'application/json');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = $this->createRequestEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);

        $request = $event->getRequest();

        self::assertTrue($request->attributes->has('data'));

        $data = $event->getRequest()->attributes->get('data');
        self::assertInstanceOf(ParameterBag::class, $data);

        self::assertSame('value', $data->get('test'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\RequestBodyListener::onKernelRequest
     */
    public function testOnKernelRequestWithNonApiRoute(): void
    {
        $this->decoderMock->expects(self::never())->method('decode');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/', Request::METHOD_POST, [], [], [], [], '{"test": "value"}');
        $request->headers->set('Content-Type', 'application/json');

        $event = $this->createRequestEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertFalse($event->getRequest()->attributes->has('data'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\RequestBodyListener::onKernelRequest
     */
    public function testOnKernelRequestInSubRequest(): void
    {
        $this->decoderMock->expects(self::never())->method('decode');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/', Request::METHOD_POST, [], [], [], [], '{"test": "value"}');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $request->headers->set('Content-Type', 'application/json');

        $event = $this->createRequestEvent($kernelMock, $request, HttpKernelInterface::SUB_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertFalse($event->getRequest()->attributes->has('data'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\RequestBodyListener::isDecodeable
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\RequestBodyListener::onKernelRequest
     */
    public function testOnKernelRequestWithInvalidMethod(): void
    {
        $this->decoderMock->expects(self::never())->method('decode');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = $this->createRequestEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertFalse($event->getRequest()->attributes->has('data'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\RequestBodyListener::isDecodeable
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\RequestBodyListener::onKernelRequest
     */
    public function testOnKernelRequestWithInvalidContentType(): void
    {
        $this->decoderMock->expects(self::never())->method('decode');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/', Request::METHOD_POST, [], [], [], [], '{"test": "value"}');
        $request->headers->set('Content-Type', 'some/type');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = $this->createRequestEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertFalse($event->getRequest()->attributes->has('data'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\RequestBodyListener::onKernelRequest
     */
    public function testOnKernelRequestWithInvalidJson(): void
    {
        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Request body has an invalid format');

        $this->decoderMock
            ->expects(self::once())
            ->method('decode')
            ->with(self::identicalTo('{]'))
            ->willThrowException(new UnexpectedValueException());

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/', Request::METHOD_POST, [], [], [], [], '{]');
        $request->headers->set('Content-Type', 'application/json');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = $this->createRequestEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\RequestBodyListener::onKernelRequest
     */
    public function testOnKernelRequestWithNonArrayJson(): void
    {
        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Request body has an invalid format');

        $this->decoderMock
            ->expects(self::once())
            ->method('decode')
            ->with(self::identicalTo('42'))
            ->willReturn(42);

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/', Request::METHOD_POST, [], [], [], [], '42');
        $request->headers->set('Content-Type', 'application/json');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = $this->createRequestEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }
}
