<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Tests\Menu;

use Knp\Menu\Integration\Symfony\RoutingExtension;
use Netgen\Bundle\BlockManagerAdminBundle\Menu\MainMenuBuilder;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuFactory;
use PHPUnit\Framework\TestCase;

class MainMenuBuilderTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerAdminBundle\Menu\MainMenuBuilder
     */
    protected $builder;

    public function setUp()
    {
        $urlGeneratorMock = $this->createMock(UrlGeneratorInterface::class);
        $urlGeneratorMock
            ->expects($this->any())
            ->method('generate')
            ->will($this->returnCallback(
                    function ($route) {
                        return $route;
                    }
                )
            );

        $menuFactory = new MenuFactory();
        $menuFactory->addExtension(new RoutingExtension($urlGeneratorMock));

        $this->builder = new MainMenuBuilder($menuFactory);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Menu\MainMenuBuilder::__construct
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Menu\MainMenuBuilder::createMenu
     */
    public function testCreateMenu()
    {
        $menu = $this->builder->createMenu();

        $this->assertInstanceOf(ItemInterface::class, $menu);
        $this->assertCount(3, $menu);

        $this->assertInstanceOf(ItemInterface::class, $menu->getChild('layout_resolver'));
        $this->assertEquals(
            'ngbm_admin_layout_resolver_index',
            $menu->getChild('layout_resolver')->getUri()
        );

        $this->assertInstanceOf(ItemInterface::class, $menu->getChild('layouts'));
        $this->assertEquals(
            'ngbm_admin_layouts_index',
            $menu->getChild('layouts')->getUri()
        );

        $this->assertInstanceOf(ItemInterface::class, $menu->getChild('shared_layouts'));
        $this->assertEquals(
            'ngbm_admin_shared_layouts_index',
            $menu->getChild('shared_layouts')->getUri()
        );
    }
}
