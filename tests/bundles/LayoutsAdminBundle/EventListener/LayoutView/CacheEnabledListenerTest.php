<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\EventListener\LayoutView;

use Netgen\Bundle\LayoutsAdminBundle\EventListener\LayoutView\CacheEnabledListener;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\Event\BuildViewEvent;
use Netgen\Layouts\HttpCache\ClientInterface;
use Netgen\Layouts\HttpCache\NullClient;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\View\LayoutView;
use Netgen\Layouts\View\View\RuleView;
use Netgen\Layouts\View\ViewInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CacheEnabledListener::class)]
final class CacheEnabledListenerTest extends TestCase
{
    private CacheEnabledListener $listener;

    protected function setUp(): void
    {
        $this->listener = new CacheEnabledListener(new NullClient());
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [
                BuildViewEvent::getEventName('layout') => 'onBuildView',
                BuildViewEvent::getEventName('rule') => 'onBuildView',
            ],
            $this->listener::getSubscribedEvents(),
        );
    }

    public function testOnBuildViewWithLayoutView(): void
    {
        $view = new LayoutView(new Layout());
        $view->context = ViewInterface::CONTEXT_ADMIN;
        $event = new BuildViewEvent($view);

        $this->listener->onBuildView($event);

        self::assertTrue($event->view->hasParameter('http_cache_enabled'));
        self::assertFalse($event->view->getParameter('http_cache_enabled'));
    }

    public function testOnBuildViewWithRuleView(): void
    {
        $view = new RuleView(new Rule());
        $view->context = ViewInterface::CONTEXT_ADMIN;
        $event = new BuildViewEvent($view);

        $this->listener->onBuildView($event);

        self::assertTrue($event->view->hasParameter('http_cache_enabled'));
        self::assertFalse($event->view->getParameter('http_cache_enabled'));
    }

    public function testOnBuildViewWithNoNullClient(): void
    {
        $this->listener = new CacheEnabledListener($this->createMock(ClientInterface::class));

        $view = new LayoutView(new Layout());
        $view->context = ViewInterface::CONTEXT_ADMIN;
        $event = new BuildViewEvent($view);

        $this->listener->onBuildView($event);

        self::assertTrue($event->view->hasParameter('http_cache_enabled'));
        self::assertTrue($event->view->getParameter('http_cache_enabled'));
    }

    public function testOnBuildViewWithUnsupportedView(): void
    {
        $view = new View(new Value());
        $event = new BuildViewEvent($view);
        $this->listener->onBuildView($event);

        self::assertFalse($event->view->hasParameter('http_cache_enabled'));
    }

    public function testOnBuildViewWithWrongContext(): void
    {
        $view = new LayoutView(new Layout());
        $view->context = ViewInterface::CONTEXT_APP;
        $event = new BuildViewEvent($view);

        $this->listener->onBuildView($event);

        self::assertFalse($event->view->hasParameter('http_cache_enabled'));
    }
}
