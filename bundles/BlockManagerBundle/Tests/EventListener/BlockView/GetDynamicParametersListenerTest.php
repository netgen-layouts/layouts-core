<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener\BlockView;

use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetDynamicParametersListener;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\View\BlockView;
use Netgen\BlockManager\Event\BlockManagerEvents;
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
        $this->listener = new GetDynamicParametersListener();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetDynamicParametersListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            array(BlockManagerEvents::BUILD_VIEW => 'onBuildView'),
            $this->listener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetDynamicParametersListener::onBuildView
     */
    public function testOnBuildView()
    {
        $block = new Block(array('blockDefinition' => new BlockDefinition('def')));

        $view = new BlockView(array('valueObject' => $block));
        $event = new CollectViewParametersEvent($view);

        $this->listener->onBuildView($event);

        $this->assertEquals(
            array(
                'dynamic_parameters' => array(
                    'definition_param' => 'definition_value',
                ),
            ),
            $event->getParameters()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetDynamicParametersListener::onBuildView
     */
    public function testOnBuildViewWithNoBlockView()
    {
        $view = new View(array('valueObject' => new Value()));
        $event = new CollectViewParametersEvent($view);
        $this->listener->onBuildView($event);

        $this->assertEquals(array(), $event->getParameters());
    }
}
