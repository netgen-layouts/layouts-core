<?php

namespace Netgen\BlockManager\Tests\EventListener;

use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Core\Values\Page\Zone;
use Netgen\BlockManager\Event\View\CollectViewParametersEvent;
use Netgen\BlockManager\EventListener\APILayoutViewZonesListener;
use Netgen\BlockManager\View\BlockView;
use Netgen\BlockManager\Event\View\ViewEvents;
use Netgen\BlockManager\View\LayoutView;

class APILayoutViewZonesListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configurationMock;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->configurationMock = $this->getMock(
            'Netgen\BlockManager\Configuration\ConfigurationInterface'
        );
    }

    /**
     * @covers \Netgen\BlockManager\EventListener\APILayoutViewZonesListener::__construct
     * @covers \Netgen\BlockManager\EventListener\APILayoutViewZonesListener::getSubscribedEvents
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
     * @covers \Netgen\BlockManager\EventListener\APILayoutViewZonesListener::onBuildView
     */
    public function testOnBuildView()
    {
        $layout = new Layout(
            array(
                'identifier' => 'layout',
                'zones' => array(
                    new Zone(
                        array(
                            'identifier' => 'zone1',
                        )
                    ),
                    new Zone(
                        array(
                            'identifier' => 'zone2',
                        )
                    ),
                ),
            )
        );

        $layoutView = new LayoutView();
        $layoutView->setContext('api');
        $layoutView->setLayout($layout);

        $layoutConfig = array(
            'zones' => array(
                'zone1' => array(
                    'allowed_blocks' => array('block'),
                ),
                'zone2' => array(),
            ),
        );

        $this->configurationMock
            ->expects($this->once())
            ->method('getLayoutConfig')
            ->with($this->equalTo('layout'))
            ->will($this->returnValue($layoutConfig));

        $event = new CollectViewParametersEvent($layoutView, array());

        $listener = $this->getEventListener();
        $listener->onBuildView($event);

        self::assertEquals(true, $event->getParameterBag()->has('zones'));
        self::assertEquals(
            array(
                array(
                    'identifier' => 'zone1',
                    'allowed_blocks' => array('block'),
                ),
                array(
                    'identifier' => 'zone2',
                    'allowed_blocks' => true,
                ),
            ),
            $event->getParameterBag()->get('zones')
        );
    }

    /**
     * @covers \Netgen\BlockManager\EventListener\APILayoutViewZonesListener::onBuildView
     */
    public function testOnBuildViewWithNoLayoutView()
    {
        $blockView = new BlockView();

        $this->configurationMock
            ->expects($this->never())
            ->method('getLayoutConfig');

        $event = new CollectViewParametersEvent($blockView, array());

        $listener = $this->getEventListener();
        $listener->onBuildView($event);

        self::assertEquals(false, $event->getParameterBag()->has('zones'));
    }

    /**
     * @covers \Netgen\BlockManager\EventListener\APILayoutViewZonesListener::onBuildView
     */
    public function testOnBuildViewWithNoApiContext()
    {
        $layoutView = new LayoutView();

        $this->configurationMock
            ->expects($this->never())
            ->method('getLayoutConfig');

        $event = new CollectViewParametersEvent($layoutView, array());

        $listener = $this->getEventListener();
        $listener->onBuildView($event);

        self::assertEquals(false, $event->getParameterBag()->has('zones'));
    }

    /**
     * Returns the listener under test.
     *
     * @return \Netgen\BlockManager\EventListener\APILayoutViewZonesListener
     */
    protected function getEventListener()
    {
        return new APILayoutViewZonesListener(
            $this->configurationMock
        );
    }
}
