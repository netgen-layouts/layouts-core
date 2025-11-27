<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Event;

use Netgen\Layouts\Event\CollectViewParametersEvent;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CollectViewParametersEvent::class)]
final class CollectViewParametersEventTest extends TestCase
{
    private CollectViewParametersEvent $event;

    private View $view;

    protected function setUp(): void
    {
        $this->view = new View(new Value());

        $this->event = new CollectViewParametersEvent($this->view);
    }

    public function testGetParameters(): void
    {
        self::assertSame([], $this->event->parameters);
    }

    public function testAddParameter(): void
    {
        $this->event->addParameter('param', 'value');
        self::assertSame(['param' => 'value'], $this->event->parameters);
    }

    public function testGetView(): void
    {
        self::assertSame($this->view, $this->event->view);
    }
}
