<?php

namespace Netgen\BlockManager\Tests\Event\View;

use Netgen\BlockManager\Event\View\CollectViewParametersEvent;
use Netgen\BlockManager\Tests\View\Stubs\View;

class CollectViewParametersEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Event\View\CollectViewParametersEvent::__construct
     * @covers \Netgen\BlockManager\Event\View\CollectViewParametersEvent::getView
     * @covers \Netgen\BlockManager\Event\View\CollectViewParametersEvent::getViewParameters
     * @covers \Netgen\BlockManager\Event\View\CollectViewParametersEvent::getBuilderParameters
     * @covers \Netgen\BlockManager\Event\View\CollectViewParametersEvent::getParameterBag
     */
    public function testEvent()
    {
        $event = new CollectViewParametersEvent(new View(), array('param' => 'value'));

        self::assertEquals(new View(), $event->getView());
        self::assertEquals(array(), $event->getViewParameters());
        self::assertEquals(array('param' => 'value'), $event->getBuilderParameters());
        self::assertInstanceOf('Symfony\Component\HttpFoundation\ParameterBag', $event->getParameterBag());
    }
}
