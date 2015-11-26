<?php

namespace Netgen\BlockManager\Tests\EventListener;

use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Core\Values\Page\Zone;
use Netgen\BlockManager\Event\View\CollectViewParametersEvent;
use Netgen\BlockManager\EventListener\APILayoutViewBlockPositionsListener;
use Netgen\BlockManager\View\BlockView;
use Netgen\BlockManager\Event\View\ViewEvents;
use Netgen\BlockManager\View\LayoutView;

class APILayoutViewBlockPositionsListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\EventListener\APILayoutViewBlockPositionsListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $listener = $this->getEventListener();
        self::assertEquals(
            array(ViewEvents::BUILD_VIEW => 'onBuildView'),
            $listener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\BlockManager\EventListener\APILayoutViewBlockPositionsListener::onBuildView
     */
    public function testOnBuildView()
    {
        $block = new Block(
            array(
                'id' => 24,
            )
        );

        $layout = new Layout(
            array(
                'identifier' => 'layout',
                'zones' => array(
                    new Zone(
                        array(
                            'identifier' => 'zone1',
                            'blocks' => array($block)
                        )
                    ),
                    new Zone(
                        array(
                            'identifier' => 'zone2',
                            'blocks' => array()
                        )
                    )
                )
            )
        );

        $layoutView = new LayoutView();
        $layoutView->setContext('api');
        $layoutView->setLayout($layout);

        $event = new CollectViewParametersEvent($layoutView, array());

        $listener = $this->getEventListener();
        $listener->onBuildView($event);

        self::assertEquals(true, $event->getParameterBag()->has('positions'));
        self::assertEquals(
            array(
                array(
                    'zone' => 'zone1',
                    'blocks' => array(24)
                ),
                array(
                    'zone' => 'zone2',
                    'blocks' => array()
                ),
            ),
            $event->getParameterBag()->get('positions')
        );
    }

    /**
     * @covers \Netgen\BlockManager\EventListener\APILayoutViewBlockPositionsListener::onBuildView
     */
    public function testOnBuildViewWithNoLayoutView()
    {
        $blockView = new BlockView();

        $event = new CollectViewParametersEvent($blockView, array());

        $listener = $this->getEventListener();
        $listener->onBuildView($event);

        self::assertEquals(false, $event->getParameterBag()->has('zones'));
    }

    /**
     * @covers \Netgen\BlockManager\EventListener\APILayoutViewBlockPositionsListener::onBuildView
     */
    public function testOnBuildViewWithNoApiContext()
    {
        $layoutView = new LayoutView();

        $event = new CollectViewParametersEvent($layoutView, array());

        $listener = $this->getEventListener();
        $listener->onBuildView($event);

        self::assertEquals(false, $event->getParameterBag()->has('zones'));
    }

    /**
     * Returns the listener under test.
     *
     * @return \Netgen\BlockManager\EventListener\APILayoutViewBlockPositionsListener
     */
    protected function getEventListener()
    {
        return new APILayoutViewBlockPositionsListener();
    }
}
