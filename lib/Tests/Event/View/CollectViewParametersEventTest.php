<?php

namespace Netgen\BlockManager\Tests\Event\View;

use Netgen\BlockManager\Event\View\CollectViewParametersEvent;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Symfony\Component\HttpFoundation\ParameterBag;

class CollectViewParametersEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Event\View\CollectViewParametersEvent::__construct
     * @covers \Netgen\BlockManager\Event\View\CollectViewParametersEvent::getView
     * @covers \Netgen\BlockManager\Event\View\CollectViewParametersEvent::getViewParameters
     * @covers \Netgen\BlockManager\Event\View\CollectViewParametersEvent::getParameterBag
     */
    public function testEvent()
    {
        $event = new CollectViewParametersEvent(new View());

        self::assertEquals(new View(), $event->getView());
        self::assertEquals(array(), $event->getViewParameters());
        self::assertInstanceOf(ParameterBag::class, $event->getParameterBag());
    }
}
