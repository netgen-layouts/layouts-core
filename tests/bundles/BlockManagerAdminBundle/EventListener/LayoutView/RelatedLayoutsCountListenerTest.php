<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Tests\EventListener\LayoutView;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
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
        $this->assertSame(
            [BlockManagerEvents::BUILD_VIEW => 'onBuildView'],
            $this->listener::getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\LayoutView\RelatedLayoutsCountListener::__construct
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\LayoutView\RelatedLayoutsCountListener::onBuildView
     */
    public function testOnBuildView(): void
    {
        $view = new LayoutView(['layout' => new Layout(['shared' => true, 'status' => Layout::STATUS_PUBLISHED])]);
        $view->setContext(ViewInterface::CONTEXT_ADMIN);
        $event = new CollectViewParametersEvent($view);

        $this->layoutServiceMock
            ->expects($this->once())
            ->method('getRelatedLayoutsCount')
            ->with($this->equalTo(new Layout(['shared' => true, 'status' => Layout::STATUS_PUBLISHED])))
            ->will($this->returnValue(3));

        $this->listener->onBuildView($event);

        $this->assertSame(
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
        $view = new LayoutView(['layout' => new Layout(['shared' => true, 'status' => Layout::STATUS_DRAFT])]);
        $view->setContext(ViewInterface::CONTEXT_ADMIN);
        $event = new CollectViewParametersEvent($view);

        $this->layoutServiceMock
            ->expects($this->never())
            ->method('getRelatedLayoutsCount');

        $this->listener->onBuildView($event);

        $this->assertSame(
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
        $view = new LayoutView(['layout' => new Layout(['shared' => false, 'status' => Layout::STATUS_PUBLISHED])]);
        $view->setContext(ViewInterface::CONTEXT_ADMIN);
        $event = new CollectViewParametersEvent($view);

        $this->layoutServiceMock
            ->expects($this->never())
            ->method('getRelatedLayoutsCount');

        $this->listener->onBuildView($event);

        $this->assertSame(
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
        $view = new View(['value' => new Value()]);
        $event = new CollectViewParametersEvent($view);
        $this->listener->onBuildView($event);

        $this->assertSame([], $event->getParameters());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\LayoutView\RelatedLayoutsCountListener::onBuildView
     */
    public function testOnBuildViewWithWrongContext(): void
    {
        $view = new LayoutView(['layout' => new Layout()]);
        $view->setContext(ViewInterface::CONTEXT_API);
        $event = new CollectViewParametersEvent($view);

        $this->listener->onBuildView($event);

        $this->assertSame([], $event->getParameters());
    }
}
