<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Tests\EventListener;

use Netgen\Bundle\BlockManagerAdminBundle\EventListener\SetIsAdminRequestListener;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\TestCase;

class SetIsAdminRequestListenerTest extends TestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\SetIsAdminRequestListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $eventListener = new SetIsAdminRequestListener();

        $this->assertEquals(
            array(KernelEvents::REQUEST => array('onKernelRequest', 30)),
            $eventListener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\SetIsAdminRequestListener::onKernelRequest
     */
    public function testOnKernelRequest()
    {
        $eventListener = new SetIsAdminRequestListener();

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set('_route', 'ngbm_admin_layout_resolver_index');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $eventListener->onKernelRequest($event);

        $this->assertEquals(
            true,
            $event->getRequest()->attributes->get(SetIsAdminRequestListener::ADMIN_FLAG_NAME)
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\SetIsAdminRequestListener::onKernelRequest
     */
    public function testOnKernelRequestWithInvalidRoute()
    {
        $eventListener = new SetIsAdminRequestListener();

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set('_route', 'some_route');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $eventListener->onKernelRequest($event);

        $this->assertEquals(
            false,
            $event->getRequest()->attributes->get(SetIsAdminRequestListener::ADMIN_FLAG_NAME)
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\SetIsAdminRequestListener::onKernelRequest
     */
    public function testOnKernelRequestInSubRequest()
    {
        $eventListener = new SetIsAdminRequestListener();

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::SUB_REQUEST);
        $eventListener->onKernelRequest($event);

        $this->assertEquals(
            false,
            $event->getRequest()->attributes->has(SetIsAdminRequestListener::ADMIN_FLAG_NAME)
        );
    }
}
