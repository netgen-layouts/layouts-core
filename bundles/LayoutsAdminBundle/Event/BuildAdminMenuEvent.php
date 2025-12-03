<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * This event will be dispatched when the admin menu is being built.
 */
final class BuildAdminMenuEvent extends Event
{
    public function __construct(
        /**
         * Returns the factory which is used to build the menu.
         */
        public private(set) FactoryInterface $factory,
        /**
         * Returns the menu which is being built.
         */
        public private(set) ItemInterface $menu,
    ) {}
}
