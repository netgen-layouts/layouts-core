<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\EventListener\RuleView;

use Netgen\Bundle\LayoutsAdminBundle\EventListener\RuleView\RuleCountListener;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\Event\CollectViewParametersEvent;
use Netgen\Layouts\Event\LayoutsEvents;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\View\RuleView;
use Netgen\Layouts\View\ViewInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use function sprintf;

final class RuleCountListenerTest extends TestCase
{
    private MockObject $layoutResolverServiceMock;

    private RuleCountListener $listener;

    protected function setUp(): void
    {
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);

        $this->listener = new RuleCountListener($this->layoutResolverServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\RuleView\RuleCountListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [sprintf('%s.%s', LayoutsEvents::BUILD_VIEW, 'rule') => 'onBuildView'],
            $this->listener::getSubscribedEvents(),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\RuleView\RuleCountListener::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\RuleView\RuleCountListener::onBuildView
     */
    public function testOnBuildView(): void
    {
        $layout = Layout::fromArray(['status' => Layout::STATUS_PUBLISHED]);
        $view = new RuleView(Rule::fromArray(['layout' => $layout]));
        $view->setContext(ViewInterface::CONTEXT_ADMIN);
        $event = new CollectViewParametersEvent($view);

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('getRuleCountForLayout')
            ->with(self::identicalTo($layout))
            ->willReturn(3);

        $this->listener->onBuildView($event);

        self::assertSame(
            [
                'rule_count' => 3,
            ],
            $event->getParameters(),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\RuleView\RuleCountListener::onBuildView
     */
    public function testOnBuildViewWithDraftLayout(): void
    {
        $view = new RuleView(Rule::fromArray(['layout' => Layout::fromArray(['status' => Layout::STATUS_DRAFT])]));
        $view->setContext(ViewInterface::CONTEXT_ADMIN);
        $event = new CollectViewParametersEvent($view);

        $this->layoutResolverServiceMock
            ->expects(self::never())
            ->method('getRuleCountForLayout');

        $this->listener->onBuildView($event);

        self::assertSame(
            [
                'rule_count' => 0,
            ],
            $event->getParameters(),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\RuleView\RuleCountListener::onBuildView
     */
    public function testOnBuildViewWithNoRuleView(): void
    {
        $view = new View(new Value());
        $event = new CollectViewParametersEvent($view);
        $this->listener->onBuildView($event);

        self::assertSame([], $event->getParameters());
    }
}
