<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener;

use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\View\LayoutView;
use Netgen\Bundle\BlockManagerBundle\EventListener\LayoutResolverListener;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit_Framework_TestCase;

class LayoutResolverListenerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutResolverMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $globalHelperMock;

    /**
     * Sets up the test
     */
    public function setUp()
    {
        $this->layoutResolverMock = $this->getMock(
            'Netgen\BlockManager\LayoutResolver\LayoutResolverInterface'
        );

        $this->viewBuilderMock = $this->getMock(
            'Netgen\BlockManager\View\ViewBuilderInterface'
        );

        $this->globalHelperMock = $this
            ->getMockBuilder('Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\LayoutResolverListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $eventListener = $this->getLayoutResolverListener();

        self::assertEquals(
            array(KernelEvents::REQUEST => array('onKernelRequest', -255)),
            $eventListener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\LayoutResolverListener::onKernelRequest
     */
    public function testOnKernelRequest()
    {
        $layout = new Layout();
        $layoutView = new LayoutView();

        $this->layoutResolverMock
            ->expects($this->once())
            ->method('resolveLayout')
            ->will($this->returnValue($layout));

        $this->viewBuilderMock
            ->expects($this->once())
            ->method('buildView')
            ->with($this->equalTo($layout))
            ->will($this->returnValue($layoutView));

        $this->globalHelperMock
            ->expects($this->once())
            ->method('setLayoutView')
            ->with($this->equalTo($layoutView));

        $eventListener = $this->getLayoutResolverListener();

        $kernelMock = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $request = Request::create('/');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $eventListener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\LayoutResolverListener::onKernelRequest
     */
    public function testOnKernelRequestWithNoLayoutMatched()
    {
        $this->layoutResolverMock
            ->expects($this->once())
            ->method('resolveLayout')
            ->will($this->returnValue(null));

        $this->viewBuilderMock
            ->expects($this->never())
            ->method('buildView');

        $this->globalHelperMock
            ->expects($this->never())
            ->method('setLayoutView');

        $eventListener = $this->getLayoutResolverListener();

        $kernelMock = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $request = Request::create('/');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $eventListener->onKernelRequest($event);
    }

    /**
     * Returns the layout resolver listener under test
     *
     * @return \Netgen\Bundle\BlockManagerBundle\EventListener\LayoutResolverListener
     */
    protected function getLayoutResolverListener()
    {
        return new LayoutResolverListener(
            $this->layoutResolverMock,
            $this->viewBuilderMock,
            $this->globalHelperMock
        );
    }
}
