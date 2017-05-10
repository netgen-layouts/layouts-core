<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Tests\Menu;

use Knp\Menu\Integration\Symfony\RoutingExtension;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuFactory;
use Netgen\Bundle\BlockManagerAdminBundle\Menu\MainMenuBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MainMenuBuilderTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $authorizationCheckerMock;

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

        $this->authorizationCheckerMock = $this->createMock(AuthorizationCheckerInterface::class);

        $this->builder = new MainMenuBuilder($menuFactory, $this->authorizationCheckerMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Menu\MainMenuBuilder::__construct
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Menu\MainMenuBuilder::createMenu
     */
    public function testCreateMenu()
    {
        $this->authorizationCheckerMock
            ->expects($this->any())
            ->method('isGranted')
            ->with($this->equalTo('ROLE_NGBM_ADMIN'))
            ->will($this->returnValue(true));

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

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Menu\MainMenuBuilder::createMenu
     */
    public function testCreateMenuWithNoAccess()
    {
        $this->authorizationCheckerMock
            ->expects($this->any())
            ->method('isGranted')
            ->with($this->equalTo('ROLE_NGBM_ADMIN'))
            ->will($this->returnValue(false));

        $menu = $this->builder->createMenu();

        $this->assertInstanceOf(ItemInterface::class, $menu);
        $this->assertCount(0, $menu);
    }
}
