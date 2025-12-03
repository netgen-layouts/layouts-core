<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Event;

use Knp\Menu\MenuFactory;
use Knp\Menu\MenuItem;
use Netgen\Bundle\LayoutsAdminBundle\Event\BuildAdminMenuEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BuildAdminMenuEvent::class)]
final class BuildAdminMenuEventTest extends TestCase
{
    private MenuFactory $factory;

    private MenuItem $menu;

    private BuildAdminMenuEvent $event;

    protected function setUp(): void
    {
        $this->factory = new MenuFactory();
        $this->menu = new MenuItem('root', $this->factory);

        $this->event = new BuildAdminMenuEvent(
            $this->factory,
            $this->menu,
        );
    }

    public function testGetFactory(): void
    {
        self::assertSame($this->factory, $this->event->factory);
    }

    public function testGetMenu(): void
    {
        self::assertSame($this->menu, $this->event->menu);
    }
}
