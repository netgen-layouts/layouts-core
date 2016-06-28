<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener\BlockView;

use Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetDynamicParametersListener;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Event\View\CollectViewParametersEvent;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\BlockView;
use Netgen\BlockManager\Event\View\ViewEvents;
use Netgen\BlockManager\View\ViewInterface;
use PHPUnit\Framework\TestCase;

class GetDynamicParametersListenerTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetDynamicParametersListener
     */
    protected $listener;

    /**
     * Sets up the test.
     */
    public function setUp()
    {
        $this->listener = new GetDynamicParametersListener(
            array(ViewInterface::CONTEXT_VIEW)
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetDynamicParametersListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        self::assertEquals(
            array(ViewEvents::BUILD_VIEW => 'onBuildView'),
            $this->listener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetDynamicParametersListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetDynamicParametersListener::onBuildView
     */
    public function testOnBuildView()
    {
        $handlerMock = $this->createMock(BlockDefinitionHandlerInterface::class);
        $handlerMock
            ->expects($this->once())
            ->method('getDynamicParameters')
            ->with($this->equalTo(new Block()))
            ->will($this->returnValue(array('param' => 'value')));

        $blockDefinition = new BlockDefinition(
            'def',
            $handlerMock,
            $this->createMock(Configuration::class)
        );

        $view = new BlockView(new Block(), $blockDefinition);
        $view->setContext(ViewInterface::CONTEXT_VIEW);
        $event = new CollectViewParametersEvent($view);

        $this->listener->onBuildView($event);

        self::assertEquals(
            array('param' => 'value'),
            $event->getViewParameters()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetDynamicParametersListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetDynamicParametersListener::onBuildView
     */
    public function testOnBuildViewWithNoBlockView()
    {
        $view = new View(new Value());
        $event = new CollectViewParametersEvent($view);
        $this->listener->onBuildView($event);

        self::assertEquals(array(), $event->getViewParameters());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetDynamicParametersListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetDynamicParametersListener::onBuildView
     */
    public function testOnBuildViewWithWrongContext()
    {
        $blockDefinition = new BlockDefinition(
            'def',
            $this->createMock(BlockDefinitionHandlerInterface::class),
            $this->createMock(Configuration::class)
        );

        $view = new BlockView(new Block(), $blockDefinition);
        $view->setContext(ViewInterface::CONTEXT_API_VIEW);
        $event = new CollectViewParametersEvent($view);

        $this->listener->onBuildView($event);

        self::assertEquals(array(), $event->getViewParameters());
    }
}
