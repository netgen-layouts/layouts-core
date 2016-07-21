<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener;

use Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener;
use Netgen\Bundle\BlockManagerBundle\EventListener\SetIsApiRequestListener;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use PHPUnit\Framework\TestCase;

class RequestBodyListenerTest extends TestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $decoderMock = $this->createMock(DecoderInterface::class);
        $eventListener = new RequestBodyListener($decoderMock);

        $this->assertEquals(
            array(KernelEvents::REQUEST => 'onKernelRequest'),
            $eventListener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener::onKernelRequest
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener::__construct
     */
    public function testOnKernelRequest()
    {
        $decoderMock = $this->createMock(DecoderInterface::class);
        $decoderMock
            ->expects($this->once())
            ->method('decode')
            ->with($this->equalTo('{"test": "value"}'))
            ->will($this->returnValue(array('test' => 'value')));

        $eventListener = new RequestBodyListener($decoderMock);

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/', 'POST', array(), array(), array(), array(), '{"test": "value"}');
        $request->headers->set('Content-Type', 'application/json');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $eventListener->onKernelRequest($event);

        $this->assertEquals(
            'value',
            $event->getRequest()->request->get('test')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener::onKernelRequest
     */
    public function testOnKernelRequestWithNonApiRoute()
    {
        $decoderMock = $this->createMock(DecoderInterface::class);
        $decoderMock->expects($this->never())->method('decode');
        $eventListener = new RequestBodyListener($decoderMock);

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/', 'POST', array(), array(), array(), array(), '{"test": "value"}');
        $request->headers->set('Content-Type', 'application/json');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $eventListener->onKernelRequest($event);

        $this->assertEquals(
            false,
            $event->getRequest()->attributes->has('test')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener::onKernelRequest
     */
    public function testOnKernelRequestInSubRequest()
    {
        $decoderMock = $this->createMock(DecoderInterface::class);
        $decoderMock->expects($this->never())->method('decode');
        $eventListener = new RequestBodyListener($decoderMock);

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/', 'POST', array(), array(), array(), array(), '{"test": "value"}');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $request->headers->set('Content-Type', 'application/json');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::SUB_REQUEST);
        $eventListener->onKernelRequest($event);

        $this->assertEquals(
            false,
            $event->getRequest()->attributes->has('test')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener::onKernelRequest
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener::isDecodeable
     */
    public function testOnKernelRequestWithInvalidMethod()
    {
        $decoderMock = $this->createMock(DecoderInterface::class);
        $decoderMock->expects($this->never())->method('decode');
        $eventListener = new RequestBodyListener($decoderMock);

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $eventListener->onKernelRequest($event);

        $this->assertEquals(
            false,
            $event->getRequest()->attributes->has('test')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener::onKernelRequest
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener::isDecodeable
     */
    public function testOnKernelRequestWithInvalidContentType()
    {
        $decoderMock = $this->createMock(DecoderInterface::class);
        $decoderMock->expects($this->never())->method('decode');
        $eventListener = new RequestBodyListener($decoderMock);

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/', 'POST', array(), array(), array(), array(), '{"test": "value"}');
        $request->headers->set('Content-Type', 'some/type');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $eventListener->onKernelRequest($event);

        $this->assertEquals(
            false,
            $event->getRequest()->attributes->has('test')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener::onKernelRequest
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function testOnKernelRequestWithInvalidJson()
    {
        $decoderMock = $this->createMock(DecoderInterface::class);
        $decoderMock
            ->expects($this->once())
            ->method('decode')
            ->with($this->equalTo('{]'))
            ->will($this->throwException(new UnexpectedValueException()));

        $eventListener = new RequestBodyListener($decoderMock);

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/', 'POST', array(), array(), array(), array(), '{]');
        $request->headers->set('Content-Type', 'application/json');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $eventListener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener::onKernelRequest
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function testOnKernelRequestWithNonArrayJson()
    {
        $decoderMock = $this->createMock(DecoderInterface::class);
        $decoderMock
            ->expects($this->once())
            ->method('decode')
            ->with($this->equalTo('42'))
            ->will($this->returnValue(42));

        $eventListener = new RequestBodyListener($decoderMock);

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/', 'POST', array(), array(), array(), array(), '42');
        $request->headers->set('Content-Type', 'application/json');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $eventListener->onKernelRequest($event);
    }
}
