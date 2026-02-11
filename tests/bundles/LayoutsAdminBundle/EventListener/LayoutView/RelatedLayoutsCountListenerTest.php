<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\EventListener\LayoutView;

use Netgen\Bundle\LayoutsAdminBundle\EventListener\LayoutView\RelatedLayoutsCountListener;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Status;
use Netgen\Layouts\Event\BuildViewEvent;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\View\LayoutView;
use Netgen\Layouts\View\ViewInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(RelatedLayoutsCountListener::class)]
final class RelatedLayoutsCountListenerTest extends TestCase
{
    private Stub&LayoutService $layoutServiceStub;

    private RelatedLayoutsCountListener $listener;

    protected function setUp(): void
    {
        $this->layoutServiceStub = self::createStub(LayoutService::class);

        $this->listener = new RelatedLayoutsCountListener($this->layoutServiceStub);
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [BuildViewEvent::getEventName('layout') => 'onBuildView'],
            $this->listener::getSubscribedEvents(),
        );
    }

    public function testOnBuildView(): void
    {
        $layout = Layout::fromArray(['isShared' => true, 'status' => Status::Published]);
        $view = new LayoutView($layout);
        $view->context = ViewInterface::CONTEXT_ADMIN;
        $event = new BuildViewEvent($view);

        $this->layoutServiceStub
            ->method('getRelatedLayoutsCount')
            ->willReturn(3);

        $this->listener->onBuildView($event);

        self::assertTrue($event->view->hasParameter('related_layouts_count'));
        self::assertSame(3, $event->view->getParameter('related_layouts_count'));
    }

    public function testOnBuildViewWithDraftLayout(): void
    {
        $view = new LayoutView(Layout::fromArray(['isShared' => true, 'status' => Status::Draft]));
        $view->context = ViewInterface::CONTEXT_ADMIN;
        $event = new BuildViewEvent($view);

        $this->listener->onBuildView($event);

        self::assertTrue($event->view->hasParameter('related_layouts_count'));
        self::assertSame(0, $event->view->getParameter('related_layouts_count'));
    }

    public function testOnBuildViewWithNonSharedLayout(): void
    {
        $view = new LayoutView(Layout::fromArray(['isShared' => false, 'status' => Status::Published]));
        $view->context = ViewInterface::CONTEXT_ADMIN;
        $event = new BuildViewEvent($view);

        $this->listener->onBuildView($event);

        self::assertTrue($event->view->hasParameter('related_layouts_count'));
        self::assertSame(0, $event->view->getParameter('related_layouts_count'));
    }

    public function testOnBuildViewWithNoLayoutView(): void
    {
        $view = new View(new Value());
        $event = new BuildViewEvent($view);
        $this->listener->onBuildView($event);

        self::assertFalse($event->view->hasParameter('related_layouts_count'));
    }

    public function testOnBuildViewWithWrongContext(): void
    {
        $view = new LayoutView(new Layout());
        $view->context = ViewInterface::CONTEXT_APP;
        $event = new BuildViewEvent($view);

        $this->listener->onBuildView($event);

        self::assertFalse($event->view->hasParameter('related_layouts_count'));
    }
}
