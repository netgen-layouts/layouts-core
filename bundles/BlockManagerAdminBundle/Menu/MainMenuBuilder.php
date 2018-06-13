<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Menu;

use Knp\Menu\FactoryInterface;
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

    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->factory = $factory;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Builds the main menu.
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createMenu()
    {
        $menu = $this->factory->createItem('root');

        if ($this->authorizationChecker->isGranted('ROLE_NGBM_ADMIN')) {
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

        return $menu;
    }
}
