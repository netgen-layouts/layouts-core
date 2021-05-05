<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Event;

use Knp\Menu\MenuFactory;
use Knp\Menu\MenuItem;
use Netgen\Bundle\LayoutsAdminBundle\Event\ConfigureMenuEvent;
use PHPUnit\Framework\TestCase;

final class ConfigureMenuEventTest extends TestCase
{
    private MenuFactory $factory;

    private MenuItem $menu;

    private ConfigureMenuEvent $event;

    protected function setUp(): void
    {
        $this->factory = new MenuFactory();
        $this->menu = new MenuItem('root', $this->factory);

        $this->event = new ConfigureMenuEvent(
            $this->factory,
            $this->menu,
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Event\ConfigureMenuEvent::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Event\ConfigureMenuEvent::getFactory
     */
    public function testGetFactory(): void
    {
        self::assertSame($this->factory, $this->event->getFactory());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Event\ConfigureMenuEvent::getMenu
     */
    public function testGetMenu(): void
    {
        self::assertSame($this->menu, $this->event->getMenu());
    }
}
