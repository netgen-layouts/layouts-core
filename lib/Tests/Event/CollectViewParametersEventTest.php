<?php

namespace Netgen\BlockManager\Tests\Event;

use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use PHPUnit\Framework\TestCase;

class CollectViewParametersEventTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Event\CollectViewParametersEvent
     */
    protected $event;

    public function setUp()
    {
        $this->event = new CollectViewParametersEvent(new View(new Value()));
    }

    /**
     * @covers \Netgen\BlockManager\Event\CollectViewParametersEvent::__construct
     * @covers \Netgen\BlockManager\Event\CollectViewParametersEvent::getParameters
     */
    public function testGetParameters()
    {
        $this->assertEquals(array(), $this->event->getParameters());
    }

    /**
     * @covers \Netgen\BlockManager\Event\CollectViewParametersEvent::getView
     */
    public function testGetView()
    {
        $this->assertEquals(new View(new Value()), $this->event->getView());
    }
}
