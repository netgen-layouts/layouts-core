<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Event;

use Netgen\Layouts\Event\BuildViewEvent;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BuildViewEvent::class)]
final class BuildViewEventTest extends TestCase
{
    private BuildViewEvent $event;

    private View $view;

    protected function setUp(): void
    {
        $this->view = new View(new Value());

        $this->event = new BuildViewEvent($this->view);
    }

    public function testGetView(): void
    {
        self::assertSame($this->view, $this->event->view);
    }

    public function getEventName(): void
    {
        self::assertSame('nglayouts.view.build_view.my_view', BuildViewEvent::getEventName('my_view'));
    }
}
