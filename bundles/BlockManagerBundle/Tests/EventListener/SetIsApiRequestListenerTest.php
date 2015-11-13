<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener;

use Netgen\Bundle\BlockManagerBundle\EventListener\SetIsApiRequestListener;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit_Framework_TestCase;

class SetIsApiRequestListenerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\SetIsApiRequestListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $eventListener = new SetIsApiRequestListener();

        self::assertEquals(
            array(KernelEvents::REQUEST => 'onKernelRequest'),
            $eventListener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\SetIsApiRequestListener::onKernelRequest
     */
    public function testOnKernelRequest()
    {
        $eventListener = new SetIsApiRequestListener();

        $kernelMock = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $request = Request::create('/');
        $request->attributes->set('_route', 'netgen_block_manager_api_v1_load_block');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $eventListener->onKernelRequest($event);

        self::assertEquals(
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

        $kernelMock = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $request = Request::create('/');
        $request->attributes->set('_route', 'some_route');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $eventListener->onKernelRequest($event);

        self::assertEquals(
            false,
            $event->getRequest()->attributes->get(SetIsApiRequestListener::API_FLAG_NAME)
        );
    }
}
