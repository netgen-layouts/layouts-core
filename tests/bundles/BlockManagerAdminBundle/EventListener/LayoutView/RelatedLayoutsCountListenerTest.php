<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Tests\EventListener\LayoutView;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\Tests\API\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\View\LayoutView;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerAdminBundle\EventListener\LayoutView\RelatedLayoutsCountListener;
use PHPUnit\Framework\TestCase;

final class RelatedLayoutsCountListenerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $layoutServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerAdminBundle\EventListener\LayoutView\RelatedLayoutsCountListener
     */
    private $listener;

    public function setUp(): void
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        $this->listener = new RelatedLayoutsCountListener($this->layoutServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\LayoutView\RelatedLayoutsCountListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [sprintf('%s.%s', BlockManagerEvents::BUILD_VIEW, 'layout') => 'onBuildView'],
            $this->listener::getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\LayoutView\RelatedLayoutsCountListener::__construct
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\LayoutView\RelatedLayoutsCountListener::onBuildView
     */
    public function testOnBuildView(): void
    {
        $layout = Layout::fromArray(['shared' => true, 'status' => Layout::STATUS_PUBLISHED]);
        $view = new LayoutView($layout);
        $view->setContext(ViewInterface::CONTEXT_ADMIN);
        $event = new CollectViewParametersEvent($view);

        $this->layoutServiceMock
            ->expects(self::once())
            ->method('getRelatedLayoutsCount')
            ->with(self::identicalTo($layout))
            ->will(self::returnValue(3));

        $this->listener->onBuildView($event);

        self::assertSame(
            [
                'related_layouts_count' => 3,
            ],
            $event->getParameters()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\LayoutView\RelatedLayoutsCountListener::onBuildView
     */
    public function testOnBuildViewWithDraftLayout(): void
    {
        $view = new LayoutView(Layout::fromArray(['shared' => true, 'status' => Layout::STATUS_DRAFT]));
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
            $event->getParameters()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\LayoutView\RelatedLayoutsCountListener::onBuildView
     */
    public function testOnBuildViewWithNonSharedLayout(): void
    {
        $view = new LayoutView(Layout::fromArray(['shared' => false, 'status' => Layout::STATUS_PUBLISHED]));
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
            $event->getParameters()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\LayoutView\RelatedLayoutsCountListener::onBuildView
     */
    public function testOnBuildViewWithNoLayoutView(): void
    {
        $view = new View(new Value());
        $event = new CollectViewParametersEvent($view);
        $this->listener->onBuildView($event);

        self::assertSame([], $event->getParameters());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\LayoutView\RelatedLayoutsCountListener::onBuildView
     */
    public function testOnBuildViewWithWrongContext(): void
    {
        $view = new LayoutView(new Layout());
        $view->setContext(ViewInterface::CONTEXT_API);
        $event = new CollectViewParametersEvent($view);

        $this->listener->onBuildView($event);

        self::assertSame([], $event->getParameters());
    }
}
