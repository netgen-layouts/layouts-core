<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Event;

use Netgen\Layouts\Event\RenderViewEvent;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RenderViewEvent::class)]
final class RenderViewEventTest extends TestCase
{
    private RenderViewEvent $event;

    private View $view;

    protected function setUp(): void
    {
        $this->view = new View(new Value());

        $this->event = new RenderViewEvent($this->view);
    }

    public function testGetView(): void
    {
        self::assertSame($this->view, $this->event->view);
    }

    public function getEventName(): void
    {
        self::assertSame('nglayouts.view.render_view.my_view', RenderViewEvent::getEventName('my_view'));
    }
}
