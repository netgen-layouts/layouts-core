<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Event;

use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use PHPUnit\Framework\TestCase;

final class CollectViewParametersEventTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Event\CollectViewParametersEvent
     */
    private $event;

    public function setUp()
    {
        $this->event = new CollectViewParametersEvent(new View(['value' => new Value()]));
    }

    /**
     * @covers \Netgen\BlockManager\Event\CollectViewParametersEvent::__construct
     * @covers \Netgen\BlockManager\Event\CollectViewParametersEvent::getParameters
     */
    public function testGetParameters()
    {
        $this->assertEquals([], $this->event->getParameters());
    }

    /**
     * @covers \Netgen\BlockManager\Event\CollectViewParametersEvent::addParameter
     * @covers \Netgen\BlockManager\Event\CollectViewParametersEvent::getParameters
     */
    public function testAddParameter()
    {
        $this->event->addParameter('param', 'value');
        $this->assertEquals(['param' => 'value'], $this->event->getParameters());
    }

    /**
     * @covers \Netgen\BlockManager\Event\CollectViewParametersEvent::getView
     */
    public function testGetView()
    {
        $this->assertEquals(new View(['value' => new Value()]), $this->event->getView());
    }
}
