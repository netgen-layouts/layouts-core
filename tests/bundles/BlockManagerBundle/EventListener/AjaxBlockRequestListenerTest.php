<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener;

use Netgen\Bundle\BlockManagerBundle\EventListener\AjaxBlockRequestListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class AjaxBlockRequestListenerTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\EventListener\AjaxBlockRequestListener
     */
    private $listener;

    public function setUp()
    {
        $this->listener = new AjaxBlockRequestListener();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\AjaxBlockRequestListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            array(KernelEvents::REQUEST => array('onKernelRequest', 10)),
            $this->listener->getSubscribedEvents()
        );
    }

    /**
     * @param string $uri
     * @param string $filteredUri
     *
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\AjaxBlockRequestListener::onKernelRequest
     * @dataProvider onKernelRequestDataProvider
     */
    public function testOnKernelRequest($uri, $filteredUri)
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create($uri);

        $request->attributes->set('_route', 'ngbm_ajax_block');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);

        $this->assertTrue($event->getRequest()->attributes->has('ngbmContextUri'));
        $this->assertEquals($filteredUri, $event->getRequest()->attributes->get('ngbmContextUri'));
    }

    public function onKernelRequestDataProvider()
    {
        return array(
            array('/test/uri', '/test/uri'),
            array('/test/uri?page=13', '/test/uri'),
            array('/test/uri?var=value&page=abc', '/test/uri?var=value&page=abc'),
            array('/test/uri?page=13&var=value', '/test/uri?var=value'),
            array('/test/uri?page=13&page=14', '/test/uri'),
            array('/test/uri?page=13&page=14&var=value', '/test/uri?var=value'),
            array('/test/uri?var=value&page=13', '/test/uri?var=value'),
            array('/test/uri?var=value&page=13&page=14', '/test/uri?var=value'),
            array('/test/uri?var=value&page=13&var2=value2&page=14', '/test/uri?var=value&var2=value2'),
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\AjaxBlockRequestListener::onKernelRequest
     */
    public function testOnKernelRequestInSubRequest()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::SUB_REQUEST);
        $this->listener->onKernelRequest($event);

        $this->assertFalse($event->getRequest()->attributes->has('ngbmContextUri'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\AjaxBlockRequestListener::onKernelRequest
     */
    public function testOnKernelRequestWithInvalidRoute()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('_route', 'some_route');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);

        $this->assertFalse($event->getRequest()->attributes->has('ngbmContextUri'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\AjaxBlockRequestListener::onKernelRequest
     */
    public function testOnKernelRequestWithExistingContextUri()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('_route', 'ngbm_ajax_block');
        $request->attributes->set('ngbmContextUri', '/some/uri');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);

        $this->assertEquals('/some/uri', $event->getRequest()->attributes->get('ngbmContextUri'));
    }
}
