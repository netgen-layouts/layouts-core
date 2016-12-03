<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener;

use Netgen\Bundle\BlockManagerBundle\EventListener\SetIsApiRequestListener;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\TestCase;

class SetIsApiRequestListenerTest extends TestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\SetIsApiRequestListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $eventListener = new SetIsApiRequestListener();

        $this->assertEquals(
            array(KernelEvents::REQUEST => array('onKernelRequest', 30)),
            $eventListener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\SetIsApiRequestListener::onKernelRequest
     */
    public function testOnKernelRequest()
    {
        $eventListener = new SetIsApiRequestListener();

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set('_route', 'ngbm_api_v1_load_block');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $eventListener->onKernelRequest($event);

        $this->assertEquals(
            true,
            $event->getRequest()->attributes->get(SetIsApiRequestListener::API_FLAG_NAME)
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\SetIsApiRequestListener::onKernelRequest
     */
    public function testOnKernelRequestWithInvalidRoute()
    {
        $eventListener = new SetIsApiRequestListener();

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set('_route', 'some_route');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $eventListener->onKernelRequest($event);

        $this->assertEquals(
            false,
            $event->getRequest()->attributes->get(SetIsApiRequestListener::API_FLAG_NAME)
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\SetIsApiRequestListener::onKernelRequest
     */
    public function testOnKernelRequestInSubRequest()
    {
        $eventListener = new SetIsApiRequestListener();

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::SUB_REQUEST);
        $eventListener->onKernelRequest($event);

        $this->assertEquals(
            false,
            $event->getRequest()->attributes->has(SetIsApiRequestListener::API_FLAG_NAME)
        );
    }
}
