<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class ConfigureMenuEvent extends Event
{
    public function __construct(
        private FactoryInterface $factory,
        private ItemInterface $menu,
    ) {}

    /**
     * Returns the factory which is used to build the menu.
     */
    public function getFactory(): FactoryInterface
    {
        return $this->factory;
    }

    /**
     * Returns the menu which is being built.
     */
    public function getMenu(): ItemInterface
    {
        return $this->menu;
    }
}
