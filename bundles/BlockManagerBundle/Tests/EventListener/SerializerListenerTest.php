<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener;

use Netgen\BlockManager\Serializer\SerializableValue;
use Netgen\BlockManager\Tests\API\Stubs\Value;
use Netgen\Bundle\BlockManagerBundle\EventListener\SerializerListener;
use Netgen\Bundle\BlockManagerBundle\EventListener\SetIsApiRequestListener;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Request;

class SerializerListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\SerializerListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $serializerMock = $this->getMock('Symfony\Component\Serializer\SerializerInterface');
        $eventListener = new SerializerListener($serializerMock);

        self::assertEquals(
            array(KernelEvents::VIEW => 'onView'),
            $eventListener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\SerializerListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\SerializerListener::onView
     */
    public function testOnView()
    {
        $value = new SerializableValue(new Value(), 42);

        $serializerMock = $this->getMock('Symfony\Component\Serializer\SerializerInterface');
        $serializerMock
            ->expects($this->once())
            ->method('serialize')
            ->with(
                $this->equalTo($value),
                $this->equalTo('json')
            )
            ->will(
                $this->returnValue('serialized content')
            );

        $eventListener = new SerializerListener($serializerMock);

        $kernelMock = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseForControllerResultEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $value
        );

        $eventListener->onView($event);

        self::assertInstanceOf(
            'Symfony\Component\HttpFoundation\JsonResponse',
            $event->getResponse()
        );

        self::assertEquals(
            'serialized content',
            $event->getResponse()->getContent()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\SerializerListener::onView
     */
    public function testOnViewWithNoApiRequest()
    {
        $serializerMock = $this->getMock('Symfony\Component\Serializer\SerializerInterface');
        $eventListener = new SerializerListener($serializerMock);

        $kernelMock = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $request = Request::create('/');

        $event = new GetResponseForControllerResultEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new SerializableValue(new Value(), 42)
        );

        $eventListener->onView($event);

        self::assertNull($event->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\SerializerListener::onView
     */
    public function testOnViewInSubRequest()
    {
        $serializerMock = $this->getMock('Symfony\Component\Serializer\SerializerInterface');
        $eventListener = new SerializerListener($serializerMock);

        $kernelMock = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $request = Request::create('/');

        $event = new GetResponseForControllerResultEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            new SerializableValue(new Value(), 42)
        );

        $eventListener->onView($event);

        self::assertNull($event->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\SerializerListener::onView
     */
    public function testOnViewWithoutSupportedValue()
    {
        $serializerMock = $this->getMock('Symfony\Component\Serializer\SerializerInterface');
        $eventListener = new SerializerListener($serializerMock);

        $kernelMock = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseForControllerResultEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            42
        );

        $eventListener->onView($event);

        self::assertNull($event->getResponse());
    }
}
