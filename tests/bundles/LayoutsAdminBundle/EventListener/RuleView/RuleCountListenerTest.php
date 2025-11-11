<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\EventListener\RuleView;

use Netgen\Bundle\LayoutsAdminBundle\EventListener\RuleView\RuleCountListener;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\Status;
use Netgen\Layouts\Event\CollectViewParametersEvent;
use Netgen\Layouts\Event\LayoutsEvents;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\View\RuleView;
use Netgen\Layouts\View\ViewInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use function sprintf;

#[CoversClass(RuleCountListener::class)]
final class RuleCountListenerTest extends TestCase
{
    private MockObject $layoutResolverServiceMock;

    private RuleCountListener $listener;

    protected function setUp(): void
    {
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);

        $this->listener = new RuleCountListener($this->layoutResolverServiceMock);
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [sprintf('%s.%s', LayoutsEvents::BUILD_VIEW, 'rule') => 'onBuildView'],
            $this->listener::getSubscribedEvents(),
        );
    }

    public function testOnBuildView(): void
    {
        $layout = Layout::fromArray(['status' => Status::Published]);
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

    public function testOnBuildViewWithDraftLayout(): void
    {
        $view = new RuleView(Rule::fromArray(['layout' => Layout::fromArray(['status' => Status::Draft])]));
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

    public function testOnBuildViewWithNoRuleView(): void
    {
        $view = new View(new Value());
        $event = new CollectViewParametersEvent($view);
        $this->listener->onBuildView($event);

        self::assertSame([], $event->getParameters());
    }
}
