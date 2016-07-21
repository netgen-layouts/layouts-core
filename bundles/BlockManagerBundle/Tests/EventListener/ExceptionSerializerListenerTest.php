<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener;

use Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionSerializerListener;
use Netgen\Bundle\BlockManagerBundle\EventListener\SetIsApiRequestListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Request;
use Exception;
use PHPUnit\Framework\TestCase;

class ExceptionSerializerListenerTest extends TestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionSerializerListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $serializerMock = $this->createMock(SerializerInterface::class);
        $eventListener = new ExceptionSerializerListener($serializerMock);

        $this->assertEquals(
            array(KernelEvents::EXCEPTION => array('onException', 5)),
            $eventListener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionSerializerListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionSerializerListener::onException
     */
    public function testOnException()
    {
        $exception = new Exception();

        $serializerMock = $this->createMock(SerializerInterface::class);
        $serializerMock
            ->expects($this->once())
            ->method('serialize')
            ->with(
                $this->equalTo($exception),
                $this->equalTo('json')
            )
            ->will(
                $this->returnValue('serialized content')
            );

        $eventListener = new ExceptionSerializerListener($serializerMock);

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $eventListener->onException($event);

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
        $serializerMock = $this->createMock(SerializerInterface::class);
        $eventListener = new ExceptionSerializerListener($serializerMock);

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Exception()
        );

        $eventListener->onException($event);

        $this->assertNull($event->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionSerializerListener::onException
     */
    public function testOnExceptionInSubRequest()
    {
        $serializerMock = $this->createMock(SerializerInterface::class);
        $eventListener = new ExceptionSerializerListener($serializerMock);

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            new Exception()
        );

        $eventListener->onException($event);

        $this->assertNull($event->getResponse());
    }
}
