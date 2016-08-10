<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class MainMenuBuilder
{
    /**
     * @var \Knp\Menu\FactoryInterface
     */
    protected $factory;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;

    /**
     * Constructor.
     *
     * @param \Knp\Menu\FactoryInterface $factory
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     */
    public function __construct(FactoryInterface $factory, RequestStack $requestStack)
    {
        $this->factory = $factory;
        $this->requestStack = $requestStack;
    }

    /**
     * Builds the main menu.
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createMenu()
    {
        $menu = $this->factory->createItem('root');

        if (method_exists($menu, 'setCurrentUri')) {
            // For compatibility with KNP Menu Bundle 1.x
            $menu->setCurrentUri($this->requestStack->getCurrentRequest()->getRequestUri());
        }

        $menu
            ->addChild('layout_resolver', array('route' => 'ngbm_admin_layout_resolver_index'))
            ->setLabel('menu.main_menu.layout_resolver')
            ->setExtra('translation_domain', 'ngbm_admin');

        $menu
            ->addChild('layouts', array('route' => 'ngbm_admin_layouts_index'))
            ->setLabel('menu.main_menu.layouts')
            ->setExtra('translation_domain', 'ngbm_admin');

        $menu
            ->addChild('shared_layouts', array('route' => 'ngbm_admin_shared_layouts_index'))
            ->setLabel('menu.main_menu.shared_layouts')
            ->setExtra('translation_domain', 'ngbm_admin');

        return $menu;
    }
}
