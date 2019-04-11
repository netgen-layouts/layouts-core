<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Event;

use Netgen\Layouts\Event\CollectViewParametersEvent;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use PHPUnit\Framework\TestCase;

final class CollectViewParametersEventTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\Event\CollectViewParametersEvent
     */
    private $event;

    /**
     * @var \Netgen\Layouts\Tests\View\Stubs\View
     */
    private $view;

    public function setUp(): void
    {
        $this->view = new View(new Value());

        $this->event = new CollectViewParametersEvent($this->view);
    }

    /**
     * @covers \Netgen\Layouts\Event\CollectViewParametersEvent::__construct
     * @covers \Netgen\Layouts\Event\CollectViewParametersEvent::getParameters
     */
    public function testGetParameters(): void
    {
        self::assertSame([], $this->event->getParameters());
    }

    /**
     * @covers \Netgen\Layouts\Event\CollectViewParametersEvent::addParameter
     * @covers \Netgen\Layouts\Event\CollectViewParametersEvent::getParameters
     */
    public function testAddParameter(): void
    {
        $this->event->addParameter('param', 'value');
        self::assertSame(['param' => 'value'], $this->event->getParameters());
    }

    /**
     * @covers \Netgen\Layouts\Event\CollectViewParametersEvent::getView
     */
    public function testGetView(): void
    {
        self::assertSame($this->view, $this->event->getView());
    }
}
