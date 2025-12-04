<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\EventListener\RuleView;

use Netgen\Bundle\LayoutsAdminBundle\EventListener\RuleView\RuleCountListener;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\Status;
use Netgen\Layouts\Event\BuildViewEvent;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\View\RuleView;
use Netgen\Layouts\View\ViewInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(RuleCountListener::class)]
final class RuleCountListenerTest extends TestCase
{
    private Stub&LayoutResolverService $layoutResolverServiceStub;

    private RuleCountListener $listener;

    protected function setUp(): void
    {
        $this->layoutResolverServiceStub = self::createStub(LayoutResolverService::class);

        $this->listener = new RuleCountListener($this->layoutResolverServiceStub);
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [BuildViewEvent::getEventName('rule') => 'onBuildView'],
            $this->listener::getSubscribedEvents(),
        );
    }

    public function testOnBuildView(): void
    {
        $layout = Layout::fromArray(['status' => Status::Published]);
        $view = new RuleView(Rule::fromArray(['layout' => $layout]));
        $view->context = ViewInterface::CONTEXT_ADMIN;
        $event = new BuildViewEvent($view);

        $this->layoutResolverServiceStub
            ->method('getRuleCountForLayout')
            ->with(self::identicalTo($layout))
            ->willReturn(3);

        $this->listener->onBuildView($event);

        self::assertTrue($event->view->hasParameter('rule_count'));
        self::assertSame(3, $event->view->getParameter('rule_count'));
    }

    public function testOnBuildViewWithDraftLayout(): void
    {
        $view = new RuleView(Rule::fromArray(['layout' => Layout::fromArray(['status' => Status::Draft])]));
        $view->context = ViewInterface::CONTEXT_ADMIN;
        $event = new BuildViewEvent($view);

        $this->listener->onBuildView($event);

        self::assertTrue($event->view->hasParameter('rule_count'));
        self::assertSame(0, $event->view->getParameter('rule_count'));
    }

    public function testOnBuildViewWithNoRuleView(): void
    {
        $view = new View(new Value());
        $event = new BuildViewEvent($view);
        $this->listener->onBuildView($event);

        self::assertFalse($event->view->hasParameter('rule_count'));
    }
}
