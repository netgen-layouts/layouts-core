<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\EventListener\LayoutView;

use Netgen\Bundle\LayoutsAdminBundle\EventListener\LayoutView\RelatedLayoutsCountListener;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Status;
use Netgen\Layouts\Event\CollectViewParametersEvent;
use Netgen\Layouts\Event\LayoutsEvents;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\View\LayoutView;
use Netgen\Layouts\View\ViewInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use function sprintf;

#[CoversClass(RelatedLayoutsCountListener::class)]
final class RelatedLayoutsCountListenerTest extends TestCase
{
    private MockObject $layoutServiceMock;

    private RelatedLayoutsCountListener $listener;

    protected function setUp(): void
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        $this->listener = new RelatedLayoutsCountListener($this->layoutServiceMock);
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [sprintf('%s.%s', LayoutsEvents::BUILD_VIEW, 'layout') => 'onBuildView'],
            $this->listener::getSubscribedEvents(),
        );
    }

    public function testOnBuildView(): void
    {
        $layout = Layout::fromArray(['shared' => true, 'status' => Status::Published]);
        $view = new LayoutView($layout);
        $view->setContext(ViewInterface::CONTEXT_ADMIN);
        $event = new CollectViewParametersEvent($view);

        $this->layoutServiceMock
            ->expects(self::once())
            ->method('getRelatedLayoutsCount')
            ->with(self::identicalTo($layout))
            ->willReturn(3);

        $this->listener->onBuildView($event);

        self::assertSame(
            [
                'related_layouts_count' => 3,
            ],
            $event->getParameters(),
        );
    }

    public function testOnBuildViewWithDraftLayout(): void
    {
        $view = new LayoutView(Layout::fromArray(['shared' => true, 'status' => Status::Draft]));
        $view->setContext(ViewInterface::CONTEXT_ADMIN);
        $event = new CollectViewParametersEvent($view);

        $this->layoutServiceMock
            ->expects(self::never())
            ->method('getRelatedLayoutsCount');

        $this->listener->onBuildView($event);

        self::assertSame(
            [
                'related_layouts_count' => 0,
            ],
            $event->getParameters(),
        );
    }

    public function testOnBuildViewWithNonSharedLayout(): void
    {
        $view = new LayoutView(Layout::fromArray(['shared' => false, 'status' => Status::Published]));
        $view->setContext(ViewInterface::CONTEXT_ADMIN);
        $event = new CollectViewParametersEvent($view);

        $this->layoutServiceMock
            ->expects(self::never())
            ->method('getRelatedLayoutsCount');

        $this->listener->onBuildView($event);

        self::assertSame(
            [
                'related_layouts_count' => 0,
            ],
            $event->getParameters(),
        );
    }

    public function testOnBuildViewWithNoLayoutView(): void
    {
        $view = new View(new Value());
        $event = new CollectViewParametersEvent($view);
        $this->listener->onBuildView($event);

        self::assertSame([], $event->getParameters());
    }

    public function testOnBuildViewWithWrongContext(): void
    {
        $view = new LayoutView(new Layout());
        $view->setContext(ViewInterface::CONTEXT_APP);
        $event = new CollectViewParametersEvent($view);

        $this->listener->onBuildView($event);

        self::assertSame([], $event->getParameters());
    }
}
