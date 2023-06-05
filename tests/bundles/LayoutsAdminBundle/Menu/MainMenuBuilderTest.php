<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Menu;

use Knp\Menu\Integration\Symfony\RoutingExtension;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuFactory;
use Netgen\Bundle\LayoutsAdminBundle\Menu\MainMenuBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class MainMenuBuilderTest extends TestCase
{
    private MockObject $authorizationCheckerMock;

    private MainMenuBuilder $builder;

    protected function setUp(): void
    {
        $urlGeneratorMock = $this->createMock(UrlGeneratorInterface::class);
        $urlGeneratorMock
            ->method('generate')
            ->willReturnCallback(
                static fn (string $route): string => $route,
            );

        $menuFactory = new MenuFactory();
        $menuFactory->addExtension(new RoutingExtension($urlGeneratorMock));

        $this->authorizationCheckerMock = $this->createMock(AuthorizationCheckerInterface::class);

        $this->builder = new MainMenuBuilder(
            $menuFactory,
            $this->authorizationCheckerMock,
            $this->createMock(EventDispatcherInterface::class),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Menu\MainMenuBuilder::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Menu\MainMenuBuilder::createMenu
     */
    public function testCreateMenu(): void
    {
        $this->authorizationCheckerMock
            ->method('isGranted')
            ->with(self::identicalTo('nglayouts:ui:access'))
            ->willReturn(true);

        $menu = $this->builder->createMenu();

        self::assertCount(4, $menu);

        self::assertInstanceOf(ItemInterface::class, $menu->getChild('layout_resolver'));
        self::assertSame(
            'nglayouts_admin_layout_resolver_index',
            $menu->getChild('layout_resolver')->getUri(),
        );

        self::assertInstanceOf(ItemInterface::class, $menu->getChild('layouts'));
        self::assertSame(
            'nglayouts_admin_layouts_index',
            $menu->getChild('layouts')->getUri(),
        );

        self::assertInstanceOf(ItemInterface::class, $menu->getChild('shared_layouts'));
        self::assertSame(
            'nglayouts_admin_shared_layouts_index',
            $menu->getChild('shared_layouts')->getUri(),
        );

        self::assertInstanceOf(ItemInterface::class, $menu->getChild('transfer'));
        self::assertSame(
            'nglayouts_admin_transfer_index',
            $menu->getChild('transfer')->getUri(),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Menu\MainMenuBuilder::createMenu
     */
    public function testCreateMenuWithNoAccess(): void
    {
        $this->authorizationCheckerMock
            ->method('isGranted')
            ->with(self::identicalTo('nglayouts:ui:access'))
            ->willReturn(false);

        $menu = $this->builder->createMenu();

        self::assertCount(0, $menu);
    }
}
