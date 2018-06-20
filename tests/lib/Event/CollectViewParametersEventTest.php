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

    /**
     * @var \Netgen\BlockManager\Tests\View\Stubs\View
     */
    private $view;

    public function setUp(): void
    {
        $this->view = new View(new Value());

        $this->event = new CollectViewParametersEvent($this->view);
    }

    /**
     * @covers \Netgen\BlockManager\Event\CollectViewParametersEvent::__construct
     * @covers \Netgen\BlockManager\Event\CollectViewParametersEvent::getParameters
     */
    public function testGetParameters(): void
    {
        $this->assertSame([], $this->event->getParameters());
    }

    /**
     * @covers \Netgen\BlockManager\Event\CollectViewParametersEvent::addParameter
     * @covers \Netgen\BlockManager\Event\CollectViewParametersEvent::getParameters
     */
    public function testAddParameter(): void
    {
        $this->event->addParameter('param', 'value');
        $this->assertSame(['param' => 'value'], $this->event->getParameters());
    }

    /**
     * @covers \Netgen\BlockManager\Event\CollectViewParametersEvent::getView
     */
    public function testGetView(): void
    {
        $this->assertSame($this->view, $this->event->getView());
    }
}
