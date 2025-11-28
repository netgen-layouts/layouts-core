<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Netgen\Bundle\LayoutsAdminBundle\Event\ConfigureMenuEvent;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class MainMenuBuilder
{
    public function __construct(
        private FactoryInterface $factory,
        private AuthorizationCheckerInterface $authorizationChecker,
        private EventDispatcherInterface $eventDispatcher,
    ) {}

    /**
     * Builds the main menu.
     */
    public function createMenu(): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        if ($this->authorizationChecker->isGranted('nglayouts:ui:access')) {
            $menu
                ->addChild('layout_resolver', ['route' => 'nglayouts_admin_layout_resolver_index'])
                ->setLabel('menu.main_menu.layout_resolver')
                ->setExtra('translation_domain', 'nglayouts_admin');

            $menu
                ->addChild('layouts', ['route' => 'nglayouts_admin_layouts_index'])
                ->setLabel('menu.main_menu.layouts')
                ->setExtra('translation_domain', 'nglayouts_admin');

            $menu
                ->addChild('shared_layouts', ['route' => 'nglayouts_admin_shared_layouts_index'])
                ->setLabel('menu.main_menu.shared_layouts')
                ->setExtra('translation_domain', 'nglayouts_admin');

            $menu
                ->addChild('transfer', ['route' => 'nglayouts_admin_transfer_index'])
                ->setLabel('menu.main_menu.transfer')
                ->setExtra('translation_domain', 'nglayouts_admin');
        }

        $this->eventDispatcher->dispatch(new ConfigureMenuEvent($this->factory, $menu));

        return $menu;
    }
}
