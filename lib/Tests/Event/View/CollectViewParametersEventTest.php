<?php

namespace Netgen\BlockManager\Tests\Event\View;

use Netgen\BlockManager\Event\View\CollectViewParametersEvent;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Symfony\Component\HttpFoundation\ParameterBag;

class CollectViewParametersEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Event\View\CollectViewParametersEvent
     */
    protected $event;

    public function setUp()
    {
        $this->event = new CollectViewParametersEvent(new View(new Value()));
    }

    /**
     * @covers \Netgen\BlockManager\Event\View\CollectViewParametersEvent::__construct
     * @covers \Netgen\BlockManager\Event\View\CollectViewParametersEvent::getViewParameters
     */
    public function testGetViewParameters()
    {
        self::assertEquals(array(), $this->event->getViewParameters());
    }

    /**
     * @covers \Netgen\BlockManager\Event\View\CollectViewParametersEvent::getParameterBag
     */
    public function testGetParameterBag()
    {
        self::assertInstanceOf(ParameterBag::class, $this->event->getParameterBag());
    }

    /**
     * @covers \Netgen\BlockManager\Event\View\CollectViewParametersEvent::getView
     */
    public function testGetView()
    {
        self::assertEquals(new View(new Value()), $this->event->getView());
    }
}
