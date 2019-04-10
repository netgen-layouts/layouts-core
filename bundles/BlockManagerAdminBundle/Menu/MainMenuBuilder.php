<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Netgen\BlockManager\Utils\BackwardsCompatibility\EventDispatcherProxy;
use Netgen\Bundle\BlockManagerAdminBundle\Event\BlockManagerAdminEvents;
use Netgen\Bundle\BlockManagerAdminBundle\Event\ConfigureMenuEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class MainMenuBuilder
{
    /**
     * @var \Knp\Menu\FactoryInterface
     */
    private $factory;

    /**
     * @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var \Netgen\BlockManager\Utils\BackwardsCompatibility\EventDispatcherProxy
     */
    private $eventDispatcher;

    public function __construct(
        FactoryInterface $factory,
        AuthorizationCheckerInterface $authorizationChecker,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->factory = $factory;
        $this->authorizationChecker = $authorizationChecker;
        $this->eventDispatcher = new EventDispatcherProxy($eventDispatcher);
    }

    /**
     * Builds the main menu.
     */
    public function createMenu(): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        if ($this->authorizationChecker->isGranted('nglayouts:ui:access')) {
            $menu
                ->addChild('layout_resolver', ['route' => 'ngbm_admin_layout_resolver_index'])
                ->setLabel('menu.main_menu.layout_resolver')
                ->setExtra('translation_domain', 'ngbm_admin');

            $menu
                ->addChild('layouts', ['route' => 'ngbm_admin_layouts_index'])
                ->setLabel('menu.main_menu.layouts')
                ->setExtra('translation_domain', 'ngbm_admin');

            $menu
                ->addChild('shared_layouts', ['route' => 'ngbm_admin_shared_layouts_index'])
                ->setLabel('menu.main_menu.shared_layouts')
                ->setExtra('translation_domain', 'ngbm_admin');
        }

        $this->eventDispatcher->dispatch(
            new ConfigureMenuEvent($this->factory, $menu),
            BlockManagerAdminEvents::CONFIGURE_MENU
        );

        return $menu;
    }
}
