<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener;

use Exception;
use Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionSerializerListener;
use Netgen\Bundle\BlockManagerBundle\EventListener\SetIsApiRequestListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

final class ExceptionSerializerListenerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $serializerMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionSerializerListener
     */
    private $listener;

    public function setUp()
    {
        $this->serializerMock = $this->createMock(SerializerInterface::class);

        $this->listener = new ExceptionSerializerListener($this->serializerMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionSerializerListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            [KernelEvents::EXCEPTION => ['onException', 5]],
            $this->listener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionSerializerListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionSerializerListener::onException
     */
    public function testOnException()
    {
        $exception = new Exception();

        $this->serializerMock
            ->expects($this->once())
            ->method('serialize')
            ->with(
                $this->equalTo($exception),
                $this->equalTo('json')
            )
            ->will(
                $this->returnValue('serialized content')
            );

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $this->listener->onException($event);

        $this->assertInstanceOf(
            JsonResponse::class,
            $event->getResponse()
        );

        $this->assertEquals(
            'serialized content',
            $event->getResponse()->getContent()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionSerializerListener::onException
     */
    public function testOnExceptionWithNoApiRequest()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Exception()
        );

        $this->listener->onException($event);

        $this->assertFalse($event->hasResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionSerializerListener::onException
     */
    public function testOnExceptionInSubRequest()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            new Exception()
        );

        $this->listener->onException($event);

        $this->assertFalse($event->hasResponse());
    }
}
