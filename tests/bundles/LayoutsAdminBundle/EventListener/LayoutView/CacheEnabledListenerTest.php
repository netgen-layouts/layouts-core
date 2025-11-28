<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\EventListener\LayoutView;

use Netgen\Bundle\LayoutsAdminBundle\EventListener\LayoutView\CacheEnabledListener;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\Event\CollectViewParametersEvent;
use Netgen\Layouts\Event\LayoutsEvents;
use Netgen\Layouts\HttpCache\ClientInterface;
use Netgen\Layouts\HttpCache\NullClient;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\View\LayoutView;
use Netgen\Layouts\View\View\RuleView;
use Netgen\Layouts\View\ViewInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

use function sprintf;

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
                sprintf('%s.%s', LayoutsEvents::BUILD_VIEW, 'layout') => 'onBuildView',
                sprintf('%s.%s', LayoutsEvents::BUILD_VIEW, 'rule') => 'onBuildView',
            ],
            $this->listener::getSubscribedEvents(),
        );
    }

    public function testOnBuildViewWithLayoutView(): void
    {
        $view = new LayoutView(new Layout());
        $view->context = ViewInterface::CONTEXT_ADMIN;
        $event = new CollectViewParametersEvent($view);

        $this->listener->onBuildView($event);

        self::assertSame(
            [
                'http_cache_enabled' => false,
            ],
            $event->parameters,
        );
    }

    public function testOnBuildViewWithRuleView(): void
    {
        $view = new RuleView(new Rule());
        $view->context = ViewInterface::CONTEXT_ADMIN;
        $event = new CollectViewParametersEvent($view);

        $this->listener->onBuildView($event);

        self::assertSame(
            [
                'http_cache_enabled' => false,
            ],
            $event->parameters,
        );
    }

    public function testOnBuildViewWithNoNullClient(): void
    {
        $this->listener = new CacheEnabledListener($this->createMock(ClientInterface::class));

        $view = new LayoutView(new Layout());
        $view->context = ViewInterface::CONTEXT_ADMIN;
        $event = new CollectViewParametersEvent($view);

        $this->listener->onBuildView($event);

        self::assertSame(
            [
                'http_cache_enabled' => true,
            ],
            $event->parameters,
        );
    }

    public function testOnBuildViewWithUnsupportedView(): void
    {
        $view = new View(new Value());
        $event = new CollectViewParametersEvent($view);
        $this->listener->onBuildView($event);

        self::assertSame([], $event->parameters);
    }

    public function testOnBuildViewWithWrongContext(): void
    {
        $view = new LayoutView(new Layout());
        $view->context = ViewInterface::CONTEXT_APP;
        $event = new CollectViewParametersEvent($view);

        $this->listener->onBuildView($event);

        self::assertSame([], $event->parameters);
    }
}
