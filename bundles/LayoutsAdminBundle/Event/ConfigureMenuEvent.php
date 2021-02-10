<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Netgen\Layouts\Utils\BackwardsCompatibility\Event;

final class ConfigureMenuEvent extends Event
{
    private FactoryInterface $factory;

    private ItemInterface $menu;

    public function __construct(FactoryInterface $factory, ItemInterface $menu)
    {
        $this->factory = $factory;
        $this->menu = $menu;
    }

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
