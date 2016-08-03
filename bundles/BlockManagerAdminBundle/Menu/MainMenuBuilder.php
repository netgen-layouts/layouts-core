<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Menu;

use Knp\Menu\FactoryInterface;

class MainMenuBuilder
{
    /**
     * @var \Knp\Menu\FactoryInterface
     */
    protected $factory;

    /**
     * Constructor.
     *
     * @param \Knp\Menu\FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Builds the main menu.
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createMenu()
    {
        $menu = $this->factory->createItem('root');

        $menu
            ->addChild('layout_resolver', array('route' => 'ngbm_admin_layout_resolver_index'))
            ->setLabel('menu.main_menu.layout_resolver')
            ->setExtra('translation_domain', 'ngbm_admin');

        $menu
            ->addChild('shared_layouts', array('route' => 'ngbm_admin_shared_layouts_index'))
            ->setLabel('menu.main_menu.shared_layouts')
            ->setExtra('translation_domain', 'ngbm_admin');

        return $menu;
    }
}
