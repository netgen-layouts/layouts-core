<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener;

use Netgen\BlockManager\API\Exception\NotFoundException;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Layout\Resolver\LayoutResolverInterface;
use Netgen\BlockManager\Layout\Resolver\Rule;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\Target;
use Netgen\BlockManager\View\LayoutView;
use Netgen\BlockManager\View\ViewBuilderInterface;
use Netgen\Bundle\BlockManagerBundle\EventListener\LayoutResolverListener;
use Netgen\Bundle\BlockManagerBundle\EventListener\SetIsApiRequestListener;
use Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolverInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Request;

class LayoutResolverListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutResolverMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $pageLayoutResolverMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewBuilderMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper
     */
    protected $globalHelper;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\EventListener\LayoutResolverListener
     */
    protected $listener;

    /**
     * Sets up the test.
     */
    public function setUp()
    {
        $this->layoutResolverMock = $this->getMock(
            LayoutResolverInterface::class
        );

        $this->pageLayoutResolverMock = $this->getMock(
            PageLayoutResolverInterface::class
        );

        $this->layoutServiceMock = $this->getMock(
            LayoutService::class
        );

        $this->viewBuilderMock = $this->getMock(
            ViewBuilderInterface::class
        );

        $this->globalHelper = new GlobalHelper();

        $this->listener = new LayoutResolverListener(
            $this->layoutResolverMock,
            $this->pageLayoutResolverMock,
            $this->layoutServiceMock,
            $this->viewBuilderMock,
            $this->globalHelper
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\LayoutResolverListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        self::assertEquals(
            array(KernelEvents::REQUEST => array('onKernelRequest', -255)),
            $this->listener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\LayoutResolverListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\LayoutResolverListener::onKernelRequest
     */
    public function testOnKernelRequest()
    {
        $layout = new Layout();
        $layoutView = new LayoutView($layout);

        $this->layoutResolverMock
            ->expects($this->once())
            ->method('resolveLayout')
            ->will($this->returnValue(new Rule(42, new Target(array('value')))));

        $this->layoutServiceMock
            ->expects($this->once())
            ->method('loadLayout')
            ->with($this->equalTo(42))
            ->will($this->returnValue($layout));

        $this->viewBuilderMock
            ->expects($this->once())
            ->method('buildView')
            ->with($this->equalTo($layout))
            ->will($this->returnValue($layoutView));

        $kernelMock = $this->getMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertEquals($layoutView, $this->globalHelper->getLayoutView());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\LayoutResolverListener::onKernelRequest
     */
    public function testOnKernelRequestWithNoLayoutMatched()
    {
        $this->layoutResolverMock
            ->expects($this->once())
            ->method('resolveLayout')
            ->will($this->returnValue(false));

        $this->layoutServiceMock
            ->expects($this->never())
            ->method('loadLayout');

        $this->viewBuilderMock
            ->expects($this->never())
            ->method('buildView');

        $kernelMock = $this->getMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertNull($this->globalHelper->getLayoutView());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\LayoutResolverListener::onKernelRequest
     */
    public function testOnKernelRequestWithNonExistingLayout()
    {
        $this->layoutResolverMock
            ->expects($this->once())
            ->method('resolveLayout')
            ->will($this->returnValue(new Rule(42, new Target(array('value')))));

        $this->layoutServiceMock
            ->expects($this->once())
            ->method('loadLayout')
            ->will(
                $this->throwException(
                    new NotFoundException('layout', 42)
                )
            );

        $this->viewBuilderMock
            ->expects($this->never())
            ->method('buildView');

        $kernelMock = $this->getMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertNull($this->globalHelper->getLayoutView());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\LayoutResolverListener::onKernelRequest
     */
    public function testOnKernelRequestInSubRequest()
    {
        $this->layoutResolverMock
            ->expects($this->never())
            ->method('resolveLayout');

        $this->layoutServiceMock
            ->expects($this->never())
            ->method('loadLayout');

        $this->viewBuilderMock
            ->expects($this->never())
            ->method('buildView');

        $kernelMock = $this->getMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::SUB_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertNull($this->globalHelper->getLayoutView());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\LayoutResolverListener::onKernelRequest
     */
    public function testOnKernelRequestInApiRequest()
    {
        $this->layoutResolverMock
            ->expects($this->never())
            ->method('resolveLayout');

        $this->layoutServiceMock
            ->expects($this->never())
            ->method('loadLayout');

        $this->viewBuilderMock
            ->expects($this->never())
            ->method('buildView');

        $kernelMock = $this->getMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertNull($this->globalHelper->getLayoutView());
    }
}
