<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener\HttpCache;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\HttpCache\TaggerInterface;
use Netgen\BlockManager\View\View\BlockView;
use Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\BlockResponseListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class BlockResponseListenerTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $taggerMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\BlockResponseListener
     */
    private $listener;

    public function setUp()
    {
        $this->taggerMock = $this->createMock(TaggerInterface::class);

        $this->listener = new BlockResponseListener($this->taggerMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\BlockResponseListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            array(KernelEvents::VIEW => 'onView'),
            $this->listener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\BlockResponseListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\BlockResponseListener::onView
     */
    public function testOnView()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseForControllerResultEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new BlockView(array('block' => new Block()))
        );

        $this->taggerMock
            ->expects($this->once())
            ->method('tagBlock')
            ->with($this->equalTo(new Response()), $this->equalTo(new Block()));

        $this->listener->onView($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\BlockResponseListener::onView
     */
    public function testOnViewWithSubRequest()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseForControllerResultEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            new BlockView()
        );

        $this->taggerMock
            ->expects($this->never())
            ->method('tagBlock');

        $this->listener->onView($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\BlockResponseListener::onView
     */
    public function testOnViewWithoutSupportedValue()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseForControllerResultEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            42
        );

        $this->taggerMock
            ->expects($this->never())
            ->method('tagBlock');

        $this->listener->onView($event);
    }
}
