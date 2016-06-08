<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener;

use Netgen\Bundle\BlockManagerBundle\EventListener\PageLayoutListener;
use Netgen\Bundle\BlockManagerBundle\EventListener\SetIsApiRequestListener;
use Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolverInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Request;

class PageLayoutListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $pageLayoutResolverMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper
     */
    protected $globalHelper;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\EventListener\PageLayoutListener
     */
    protected $listener;

    /**
     * Sets up the test.
     */
    public function setUp()
    {
        $this->pageLayoutResolverMock = $this->createMock(
            PageLayoutResolverInterface::class
        );

        $this->globalHelper = new GlobalHelper();

        $this->listener = new PageLayoutListener(
            $this->pageLayoutResolverMock,
            $this->globalHelper
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\PageLayoutListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        self::assertEquals(
            array(KernelEvents::REQUEST => 'onKernelRequest'),
            $this->listener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\PageLayoutListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\PageLayoutListener::onKernelRequest
     */
    public function testOnKernelRequest()
    {
        $this->pageLayoutResolverMock
            ->expects($this->once())
            ->method('resolvePageLayout')
            ->will($this->returnValue('pagelayout.html.twig'));

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertEquals('pagelayout.html.twig', $this->globalHelper->getPageLayout());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\PageLayoutListener::onKernelRequest
     */
    public function testOnKernelRequestInSubRequest()
    {
        $this->pageLayoutResolverMock
            ->expects($this->never())
            ->method('resolvePageLayout');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::SUB_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertNull($this->globalHelper->getPageLayout());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\PageLayoutListener::onKernelRequest
     */
    public function testOnKernelRequestInApiRequest()
    {
        $this->pageLayoutResolverMock
            ->expects($this->never())
            ->method('resolvePageLayout');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertNull($this->globalHelper->getPageLayout());
    }
}
